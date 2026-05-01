<?php

namespace App\Http\Controllers;

use App\Jobs\BackupDatabaseJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DatabaseBackupController extends Controller
{
    private $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/Laravel');

        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    // Tampilkan halaman backup
    public function index()
    {
        $files = File::files($this->backupPath);
        
        // Filter file .zip dan .sql
        $files = array_filter($files, function($file) {
            $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            return in_array($ext, ['zip', 'sql']);
        });
        
        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return $b->getMTime() - $a->getMTime();
        });
        
        // Cek status backup yang sedang berjalan
        $backupStatus = $this->getActiveBackupStatus();
        
        // Cleanup old status files
        BackupDatabaseJob::cleanupStatusFiles();
        
        return view('content.apps.Backup.index', compact('files', 'backupStatus'));
    }

    // Buat backup baru — spawn background process, response balik <1 detik
    public function backup(Request $request)
    {
        $type      = $request->get('type', 'db');
        $timestamp = date('Y-m-d-H-i-s');
        $backupId  = \Illuminate\Support\Str::uuid()->toString();

        // Tulis status awal agar polling langsung bisa baca
        $this->updateBackupStatus($backupId, 'processing', 'Memulai backup...', 2, 'database');

        // Spawn background process — coba beberapa metode agar selalu berhasil
        try {
            $this->spawnBackgroundProcess($type, $timestamp, $backupId);
        } catch (\Throwable $e) {
            // Jika spawning gagal, tandai sebagai failed — JANGAN biarkan exception meledak
            \Illuminate\Support\Facades\Log::error('Backup spawn failed: ' . $e->getMessage());
            $this->updateBackupStatus($backupId, 'failed',
                'Gagal memulai proses backup: ' . $e->getMessage(), 0, 'database');
        }

        // Selalu return JSON segera — tidak pernah block
        return response()->json([
            'success'   => true,
            'queued'    => true,
            'backup_id' => $backupId,
            'message'   => 'Backup berjalan di background.',
        ]);
    }

    /**
     * Coba berbagai cara untuk spawn background process.
     * Tidak ada yang blocking — semua return segera.
     */
    private function spawnBackgroundProcess(string $type, string $timestamp, string $backupId): void
    {
        // Cari PHP CLI yang benar (hindari php-fpm atau cgi yang sering gagal jalankan artisan)
        $php = 'php';
        $possiblePaths = [
            PHP_BINARY,
            '/usr/bin/php',
            '/usr/local/bin/php',
            '/opt/cpanel/ea-php81/root/usr/bin/php',
            '/opt/cpanel/ea-php82/root/usr/bin/php',
            '/opt/cpanel/ea-php83/root/usr/bin/php',
        ];
        
        foreach ($possiblePaths as $path) {
            if ($path && is_executable($path) && strpos(strtolower($path), 'fpm') === false && strpos(strtolower($path), 'cgi') === false) {
                $php = escapeshellcmd($path);
                break;
            }
        }

        $artisan = base_path('artisan');
        $logFile = storage_path('logs/backup_debug.log');
        
        $args = escapeshellarg($type) . ' '
              . escapeshellarg($timestamp) . ' '
              . escapeshellarg($backupId);

        // Arahkan output ke file log agar kita bisa lihat kenapa gagal jalan
        $cmd = "nohup {$php} {$artisan} app:backup {$args} >> {$logFile} 2>&1 &";

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));

        if (function_exists('exec') && !in_array('exec', $disabled)) {
            exec($cmd);
            \Illuminate\Support\Facades\Log::info("Backup spawned with exec: $cmd");
            return;
        }

        if (function_exists('shell_exec') && !in_array('shell_exec', $disabled)) {
            shell_exec($cmd);
            \Illuminate\Support\Facades\Log::info("Backup spawned with shell_exec: $cmd");
            return;
        }

        if (function_exists('proc_open') && !in_array('proc_open', $disabled)) {
            $descriptors = [
                0 => ['file', '/dev/null', 'r'],
                1 => ['file', $logFile, 'a'],
                2 => ['file', $logFile, 'a'],
            ];
            $pipes = [];
            proc_open("{$php} {$artisan} app:backup {$args} &", $descriptors, $pipes);
            \Illuminate\Support\Facades\Log::info("Backup spawned with proc_open");
            return;
        }

        if (function_exists('popen') && !in_array('popen', $disabled)) {
            $handle = popen($cmd, 'r');
            if ($handle) pclose($handle);
            \Illuminate\Support\Facades\Log::info("Backup spawned with popen: $cmd");
            return;
        }

        // Jika semua disabled, jalan inline (Bisa timeout tapi jalan)
        $this->updateBackupStatus($backupId, 'processing',
            'Fungsi exec nonaktif, proses berjalan langsung (bisa timeout)...', 5, 'database');
            
        // Paksa jalan synchronous jika tidak bisa background
        \Illuminate\Support\Facades\Artisan::call('app:backup', [
            'type' => $type,
            'timestamp' => $timestamp,
            'backupId' => $backupId
        ]);
    }


    /**
     * Backup database secara langsung (tanpa queue) - cepat
     */
    private function backupDatabaseDirect($timestamp)
    {
        set_time_limit(120); // 2 menit cukup untuk DB
        
        $sqlFileName = 'backup-db-' . $timestamp . '.sql';
        $sqlFilePath = $this->backupPath . '/' . $sqlFileName;

        $result = $this->runMysqldump($sqlFilePath);
        
        if ($result !== true) {
            return response()->json([
                'success' => false,
                'message' => $result
            ]);
        }

        $fileSize = $this->formatBytes(filesize($sqlFilePath));
        
        return response()->json([
            'success' => true,
            'message' => "Backup database berhasil: {$sqlFileName} ({$fileSize})",
            'completed' => true
        ]);
    }

    /**
     * Full backup langsung (tanpa queue) - dengan progress tracking
     */
    private function backupFullQueue($timestamp)
    {
        set_time_limit(600); // 10 menit max
        
        $backupId = Str::uuid()->toString();
        session(['active_backup_id' => $backupId]);

        // Set initial status
        $this->updateBackupStatus($backupId, 'processing', 'Memulai backup lengkap...', 5, 'database');

        $zipFileName = 'backup-full-' . $timestamp . '.zip';
        $zipFilePath = $this->backupPath . '/' . $zipFileName;
        $sqlFileName = 'database-' . $timestamp . '.sql';
        $sqlFilePath = $this->backupPath . '/' . $sqlFileName;

        try {
            // Step 1: Backup Database (5-30%)
            $this->updateBackupStatus($backupId, 'processing', 'Menghubungkan ke database...', 6, 'database');
            $result = $this->runMysqldump($sqlFilePath, $backupId);
            
            if ($result !== true) {
                $this->updateBackupStatus($backupId, 'failed', $result, 0, 'database');
                return response()->json(['success' => false, 'message' => $result]);
            }
            
            $dbSize = $this->formatBytes(filesize($sqlFilePath));
            $this->updateBackupStatus($backupId, 'processing', "Database selesai ({$dbSize})", 30, 'database');

            // Step 2: Buat ZIP (30-40%)
            $this->updateBackupStatus($backupId, 'processing', 'Membuat file ZIP...', 35, 'zip');
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                if (file_exists($sqlFilePath)) unlink($sqlFilePath);
                $this->updateBackupStatus($backupId, 'failed', 'Gagal membuat file ZIP', 0, 'zip');
                return response()->json(['success' => false, 'message' => 'Gagal membuat file ZIP']);
            }

            $zip->addFile($sqlFilePath, 'database/' . $sqlFileName);
            $this->updateBackupStatus($backupId, 'processing', 'Database ditambahkan ke ZIP', 40, 'zip');

            // Step 3: Tambahkan storage files (40-90%)
            $storagePublicPath = storage_path('app/public');
            if (File::isDirectory($storagePublicPath)) {
                $this->updateBackupStatus($backupId, 'processing', 'Menghitung file storage...', 42, 'storage');
                
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($storagePublicPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                
                $fileList = iterator_to_array($files);
                $totalFiles = count($fileList);
                $processedFiles = 0;
                $totalSize = 0;
                
                $this->updateBackupStatus($backupId, 'processing', "Ditemukan {$totalFiles} file storage", 45, 'storage', [
                    'total_files' => $totalFiles,
                    'processed_files' => 0
                ]);
                
                foreach ($fileList as $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = 'storage/' . substr($filePath, strlen($storagePublicPath) + 1);
                        $fileSize = filesize($filePath);
                        $totalSize += $fileSize;
                        $zip->addFile($filePath, $relativePath);
                        $processedFiles++;
                        
                        // Update setiap 10 file untuk progress lebih smooth
                        if ($processedFiles % 10 === 0 || $processedFiles === $totalFiles) {
                            $progress = 45 + (($processedFiles / max($totalFiles, 1)) * 45);
                            $currentFileName = basename($filePath);
                            $this->updateBackupStatus($backupId, 'processing', 
                                "File: {$currentFileName}", 
                                round($progress), 'storage', [
                                    'total_files' => $totalFiles,
                                    'processed_files' => $processedFiles,
                                    'current_file' => $currentFileName,
                                    'total_size' => $this->formatBytes($totalSize)
                                ]);
                        }
                    }
                }
                
                $this->updateBackupStatus($backupId, 'processing', 
                    "Storage selesai ({$totalFiles} file, " . $this->formatBytes($totalSize) . ")", 
                    90, 'storage', [
                        'total_files' => $totalFiles,
                        'processed_files' => $totalFiles,
                        'total_size' => $this->formatBytes($totalSize)
                    ]);
            }

            // Step 4: Finalisasi (90-100%)
            $this->updateBackupStatus($backupId, 'processing', 'Memfinalisasi backup...', 92, 'finalize');
            $zip->close();

            // Hapus SQL temp
            if (file_exists($sqlFilePath)) unlink($sqlFilePath);

            $this->updateBackupStatus($backupId, 'processing', 'Memverifikasi file backup...', 95, 'finalize');

            // Verifikasi
            if (!file_exists($zipFilePath) || filesize($zipFilePath) === 0) {
                $this->updateBackupStatus($backupId, 'failed', 'File backup kosong', 0, 'finalize');
                return response()->json(['success' => false, 'message' => 'File backup kosong']);
            }

            $fileSize = $this->formatBytes(filesize($zipFilePath));
            $this->updateBackupStatus($backupId, 'completed', 
                "Backup selesai: {$zipFileName} ({$fileSize})", 100, 'done');

            return response()->json([
                'success' => true,
                'message' => "Backup lengkap berhasil: {$zipFileName} ({$fileSize})",
                'completed' => true,
                'queue' => false
            ]);

        } catch (\Exception $e) {
            if (file_exists($sqlFilePath)) unlink($sqlFilePath);
            if (file_exists($zipFilePath)) unlink($zipFilePath);
            
            $this->updateBackupStatus($backupId, 'failed', 'Error: ' . $e->getMessage(), 0, 'database');
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Update backup status ke file
     */
    private function updateBackupStatus($backupId, $status, $message, $progress = 0, $step = '', $details = [])
    {
        $statusFile = storage_path('app/backup_status_' . $backupId . '.json');
        file_put_contents($statusFile, json_encode([
            'status' => $status,
            'message' => $message,
            'progress' => $progress,
            'step' => $step,
            'details' => $details,
            'updated_at' => now()->toDateTimeString(),
        ]));
    }

    /**
     * Run mysqldump directly atau PHP-based backup
     */
    private function runMysqldump($filePath, $backupId = null)
    {
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        // Gunakan PHP-based backup (lebih reliable, tidak perlu mysqldump binary)
        return $this->backupDatabaseWithPHP($filePath, $dbHost, $dbPort, $dbName, $dbUser, $dbPass, $backupId);
    }

    /**
     * Backup database menggunakan PHP PDO (tanpa mysqldump) dengan progress realtime
     */
    private function backupDatabaseWithPHP($filePath, $host, $port, $database, $user, $pass, $backupId = null)
    {
        try {
            $pdo = new \PDO(
                "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
                $user,
                $pass,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );

            $output = "";
            $output .= "-- Database Backup\n";
            $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $output .= "-- Host: {$host}\n";
            $output .= "-- Database: {$database}\n\n";
            $output .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $output .= "SET time_zone = \"+00:00\";\n\n";

            // Get all tables
            $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
            $totalTables = count($tables);
            $processedTables = 0;
            $totalRows = 0;

            if ($backupId) {
                $this->updateBackupStatus($backupId, 'processing', "Ditemukan {$totalTables} tabel", 8, 'database', [
                    'total_tables' => $totalTables,
                    'current_table' => '',
                    'processed_tables' => 0,
                    'total_rows' => 0
                ]);
            }

            foreach ($tables as $table) {
                $processedTables++;
                
                // Update status: sedang backup tabel ini
                if ($backupId) {
                    $progress = 8 + (($processedTables / $totalTables) * 20); // 8-28%
                    $this->updateBackupStatus($backupId, 'processing', "Backup tabel: {$table}", round($progress), 'database', [
                        'total_tables' => $totalTables,
                        'current_table' => $table,
                        'processed_tables' => $processedTables,
                        'total_rows' => $totalRows
                    ]);
                }

                // Get create table statement
                $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
                $output .= "\n-- Table structure for `{$table}`\n";
                $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $output .= $createTable['Create Table'] . ";\n\n";

                // Get table data
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                $rowCount = count($rows);
                $totalRows += $rowCount;
                
                if ($rowCount > 0) {
                    $output .= "-- Data for `{$table}` ({$rowCount} rows)\n";
                    
                    foreach ($rows as $row) {
                        $values = array_map(function($value) use ($pdo) {
                            if ($value === null) return 'NULL';
                            return $pdo->quote($value);
                        }, $row);
                        
                        $output .= "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $output .= "\n";
                }

                // Update progress setelah selesai backup tabel
                if ($backupId) {
                    $progress = 8 + (($processedTables / $totalTables) * 20);
                    $this->updateBackupStatus($backupId, 'processing', "Selesai: {$table} ({$rowCount} baris)", round($progress), 'database', [
                        'total_tables' => $totalTables,
                        'current_table' => $table,
                        'processed_tables' => $processedTables,
                        'total_rows' => $totalRows,
                        'last_table_rows' => $rowCount
                    ]);
                }
            }

            $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

            // Write to file
            if ($backupId) {
                $this->updateBackupStatus($backupId, 'processing', "Menyimpan {$totalTables} tabel ({$totalRows} baris)...", 28, 'database', [
                    'total_tables' => $totalTables,
                    'processed_tables' => $totalTables,
                    'total_rows' => $totalRows
                ]);
            }
            
            file_put_contents($filePath, $output);

            if (!file_exists($filePath) || filesize($filePath) === 0) {
                return 'Database backup gagal: File kosong.';
            }

            return true;

        } catch (\PDOException $e) {
            return 'Database backup gagal: ' . $e->getMessage();
        } catch (\Exception $e) {
            return 'Database backup gagal: ' . $e->getMessage();
        }
    }

    // API endpoint untuk cek status backup
    public function checkStatus(Request $request)
    {
        $backupId = $request->get('id') ?? session('active_backup_id');
        
        if (!$backupId) {
            return response()->json(['status' => 'idle', 'message' => null, 'completed' => false]);
        }

        $status = BackupDatabaseJob::getStatus($backupId);

        if (!$status) {
            return response()->json(['status' => 'idle', 'message' => null, 'completed' => false]);
        }

        // Add completed flag for easier frontend handling
        $status['completed'] = ($status['status'] === 'completed');

        // Cleanup jika sudah selesai
        if (in_array($status['status'], ['completed', 'failed'])) {
            session()->forget('active_backup_id');
            $statusFile = storage_path('app/backup_status_' . $backupId . '.json');
            if (file_exists($statusFile)) unlink($statusFile);
        }

        return response()->json($status);
    }

    // Hapus backup
    public function delete($filename)
    {
        $filePath = $this->backupPath . '/' . $filename;

        if (File::exists($filePath)) {
            File::delete($filePath);
            return back()->with('success', 'Backup berhasil dihapus.');
        }

        return back()->with('error', 'File backup tidak ditemukan.');
    }

    // Download backup
    public function download($filename)
    {
        $path = storage_path('app/Laravel/' . $filename);

        if (!is_file($path)) {
            abort(404);
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $contentType = $ext === 'zip' ? 'application/zip' : 'application/sql';

        return response()->download($path, $filename, [
            'Content-Type' => $contentType,
            'Content-Length' => filesize($path),
        ]);
    }

    private function getActiveBackupStatus()
    {
        $backupId = session('active_backup_id');
        if (!$backupId) return null;

        $status = BackupDatabaseJob::getStatus($backupId);
        if (!$status) {
            session()->forget('active_backup_id');
            return null;
        }

        if (in_array($status['status'], ['completed', 'failed'])) {
            session()->forget('active_backup_id');
        }

        return $status;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}