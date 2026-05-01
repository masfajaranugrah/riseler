<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class BackupDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 menit timeout
    public $tries = 1;

    protected $type;
    protected $timestamp;
    protected $backupId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $type, string $timestamp, string $backupId)
    {
        $this->type = $type;
        $this->timestamp = $timestamp;
        $this->backupId = $backupId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $backupPath = storage_path('app/Laravel');

        // Pastikan folder backup ada
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        try {
            // Update status: processing
            $this->updateStatus('processing', 'Memproses backup...');

            if ($this->type === 'full') {
                $this->backupFull($backupPath);
            } else {
                $this->backupDatabaseOnly($backupPath);
            }

        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            $this->updateStatus('failed', 'Backup gagal: ' . $e->getMessage());
        }
    }

    /**
     * Quick backup - Database saja
     */
    private function backupDatabaseOnly($backupPath)
    {
        $this->updateStatus('processing', 'Membackup database...');

        $sqlFileName = 'backup-db-' . $this->timestamp . '.sql';
        $sqlFilePath = $backupPath . '/' . $sqlFileName;

        $result = $this->backupDatabase($sqlFilePath);
        if ($result !== true) {
            $this->updateStatus('failed', $result);
            return;
        }

        $fileSize = $this->formatBytes(filesize($sqlFilePath));
        $this->updateStatus('completed', "Backup database berhasil: {$sqlFileName} ({$fileSize})");
    }

    /**
     * Full backup - Database + Storage
     */
    private function backupFull($backupPath)
    {
        $zipFileName = 'backup-full-' . $this->timestamp . '.zip';
        $zipFilePath = $backupPath . '/' . $zipFileName;
        $sqlFileName = 'database-' . $this->timestamp . '.sql';
        $sqlFilePath = $backupPath . '/' . $sqlFileName;

        try {
            // Step 1: Backup Database (0-30%)
            $this->updateStatus('processing', 'Membackup database...', 5, 'database');
            $dbBackupResult = $this->backupDatabase($sqlFilePath);
            if ($dbBackupResult !== true) {
                $this->updateStatus('failed', $dbBackupResult, 0, 'database');
                return;
            }
            $this->updateStatus('processing', 'Database berhasil dibackup', 30, 'database');

            // Step 2: Membuat ZIP (30-40%)
            $this->updateStatus('processing', 'Membuat file ZIP...', 35, 'zip');
            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                if (file_exists($sqlFilePath)) {
                    unlink($sqlFilePath);
                }
                $this->updateStatus('failed', 'Gagal membuat file ZIP.', 0, 'zip');
                return;
            }

            // Tambahkan file SQL ke ZIP
            $zip->addFile($sqlFilePath, 'database/' . $sqlFileName);
            $this->updateStatus('processing', 'File database ditambahkan ke ZIP', 40, 'zip');

            // Step 3: Tambahkan folder storage/app/public ke ZIP (40-90%)
            $storagePublicPath = storage_path('app/public');
            if (File::isDirectory($storagePublicPath)) {
                $this->updateStatus('processing', 'Menghitung file storage...', 45, 'storage');
                
                // Hitung total file untuk progress
                $totalFiles = $this->countFilesInDirectory($storagePublicPath);
                $this->updateStatus('processing', "Ditemukan {$totalFiles} file storage", 50, 'storage');
                
                // Tambahkan file dengan progress
                $this->addFolderToZipWithProgress($zip, $storagePublicPath, 'storage', $totalFiles, 50, 90);
            }

            // Step 4: Finalisasi ZIP (90-100%)
            $this->updateStatus('processing', 'Memfinalisasi file backup...', 92, 'finalize');
            $zip->close();

            // Hapus file SQL temporary
            if (file_exists($sqlFilePath)) {
                unlink($sqlFilePath);
            }
            $this->updateStatus('processing', 'Memverifikasi file backup...', 95, 'finalize');

            // Verifikasi file ZIP
            if (!file_exists($zipFilePath) || filesize($zipFilePath) === 0) {
                $this->updateStatus('failed', 'Backup gagal: File ZIP kosong.', 0, 'finalize');
                return;
            }

            $fileSize = $this->formatBytes(filesize($zipFilePath));
            $this->updateStatus('completed', "Backup lengkap berhasil: {$zipFileName} ({$fileSize})", 100, 'done');

        } catch (\Exception $e) {
            // Cleanup
            if (file_exists($sqlFilePath)) {
                unlink($sqlFilePath);
            }
            if (file_exists($zipFilePath)) {
                unlink($zipFilePath);
            }
            throw $e;
        }
    }

    /**
     * Backup database ke file SQL
     */
    private function backupDatabase($filePath)
    {
        $dbHost = config('database.connections.mysql.host', env('DB_HOST'));
        $dbPort = config('database.connections.mysql.port', env('DB_PORT', '3306'));
        $dbName = config('database.connections.mysql.database', env('DB_DATABASE'));
        $dbUser = config('database.connections.mysql.username', env('DB_USERNAME'));
        $dbPass = config('database.connections.mysql.password', env('DB_PASSWORD'));

        $escapedPass = escapeshellarg($dbPass);

        // Cari mysqldump
        $mysqldumpPaths = [
            'mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
            '/Applications/MAMP/Library/bin/mysqldump',
            '/Applications/XAMPP/xamppfiles/bin/mysqldump',
        ];

        $mysqldump = null;
        foreach ($mysqldumpPaths as $path) {
            if (file_exists($path) || $this->commandExists($path)) {
                $mysqldump = $path;
                break;
            }
        }

        if (!$mysqldump) {
            return 'mysqldump tidak ditemukan.';
        }

        // Build command
        if (empty($dbPass)) {
            $command = sprintf(
                '%s -h %s -P %s -u %s %s > %s 2>&1',
                escapeshellarg($mysqldump),
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );
        } else {
            $command = sprintf(
                '%s -h %s -P %s -u %s -p%s %s > %s 2>&1',
                escapeshellarg($mysqldump),
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                $escapedPass,
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );
        }

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $errorOutput = implode("\n", $output);
            return 'Database backup gagal: ' . ($errorOutput ?: 'Error code: ' . $returnVar);
        }

        if (!file_exists($filePath) || filesize($filePath) === 0) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return 'Database backup gagal: File SQL kosong.';
        }

        return true;
    }

    /**
     * Tambahkan folder ke ZIP secara rekursif
     */
    private function addFolderToZip(ZipArchive $zip, $folderPath, $zipFolder)
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipFolder . '/' . substr($filePath, strlen($folderPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    /**
     * Tambahkan folder ke ZIP dengan progress tracking
     */
    private function addFolderToZipWithProgress(ZipArchive $zip, $folderPath, $zipFolder, $totalFiles, $startProgress, $endProgress)
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $processedFiles = 0;
        $progressRange = $endProgress - $startProgress;
        $lastUpdateAt = 0;

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipFolder . '/' . substr($filePath, strlen($folderPath) + 1);
                $zip->addFile($filePath, $relativePath);
                
                $processedFiles++;
                
                // Update progress setiap 10 file atau setiap 2 detik
                $currentTime = time();
                if ($processedFiles % 10 === 0 || ($currentTime - $lastUpdateAt) >= 2) {
                    $currentProgress = $startProgress + (($processedFiles / max($totalFiles, 1)) * $progressRange);
                    $this->updateStatus(
                        'processing', 
                        "Menambahkan file storage ({$processedFiles}/{$totalFiles})...", 
                        round($currentProgress), 
                        'storage'
                    );
                    $lastUpdateAt = $currentTime;
                }
            }
        }
    }

    /**
     * Hitung jumlah file dalam directory
     */
    private function countFilesInDirectory($path)
    {
        $count = 0;
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Check if command exists
     */
    private function commandExists($command)
    {
        $whereIsCommand = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $process = proc_open(
            "$whereIsCommand $command",
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes
        );

        if (is_resource($process)) {
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            return !empty(trim($stdout));
        }

        return false;
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Update backup status di file (bukan cache database)
     */
    private function updateStatus($status, $message, $progress = 0, $step = '')
    {
        $statusFile = storage_path('app/backup_status_' . $this->backupId . '.json');
        file_put_contents($statusFile, json_encode([
            'status' => $status,
            'message' => $message,
            'progress' => $progress,
            'step' => $step,
            'updated_at' => now()->toDateTimeString(),
        ]));
    }

    /**
     * Get backup status dari file
     */
    public static function getStatus($backupId)
    {
        $statusFile = storage_path('app/backup_status_' . $backupId . '.json');
        if (file_exists($statusFile)) {
            $data = json_decode(file_get_contents($statusFile), true);
            // Cleanup file jika sudah selesai atau gagal
            if (in_array($data['status'] ?? '', ['completed', 'failed'])) {
                // Keep file for a while, will be cleaned up later
            }
            return $data;
        }
        return null;
    }

    /**
     * Cleanup old status files
     */
    public static function cleanupStatusFiles()
    {
        $files = glob(storage_path('app/backup_status_*.json'));
        foreach ($files as $file) {
            if (filemtime($file) < time() - 3600) { // Older than 1 hour
                unlink($file);
            }
        }
    }

    /**
     * Handle a job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Backup job failed: ' . $exception->getMessage());
        $this->updateStatus('failed', 'Backup gagal: ' . $exception->getMessage());
    }
}