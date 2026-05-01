<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AppBackupCommand extends Command
{
    protected $signature = 'app:backup {type} {timestamp} {backupId}';
    protected $description = 'Run database/full backup in background and update status file';

    private string $backupPath;

    public function handle(): int
    {
        $type      = $this->argument('type');
        $timestamp = $this->argument('timestamp');
        $backupId  = $this->argument('backupId');

        $this->backupPath = storage_path('app/Laravel');
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }

        set_time_limit(0);
        ignore_user_abort(true);
        ini_set('memory_limit', '1024M'); // Pastikan cukup memory untuk backup

        if ($type === 'db') {
            $this->runDbBackup($timestamp, $backupId);
        } else {
            $this->runFullBackup($timestamp, $backupId);
        }

        return 0;
    }

    // ── DB-only backup ────────────────────────────────────────────────────────
    private function runDbBackup(string $timestamp, string $backupId): void
    {
        $sqlFileName = 'backup-db-' . $timestamp . '.sql';
        $sqlFilePath = $this->backupPath . '/' . $sqlFileName;

        $this->writeStatus($backupId, 'processing', 'Membackup database...', 10, 'database');

        $result = $this->dumpDatabase($sqlFilePath, $backupId);
        if ($result !== true) {
            $this->writeStatus($backupId, 'failed', $result, 0, 'database');
            return;
        }

        $fileSize = $this->fmtBytes(filesize($sqlFilePath));
        $this->writeStatus($backupId, 'completed',
            "Backup database berhasil: {$sqlFileName} ({$fileSize})", 100, 'done');
    }

    // ── Full backup ───────────────────────────────────────────────────────────
    private function runFullBackup(string $timestamp, string $backupId): void
    {
        $zipFileName = 'backup-full-' . $timestamp . '.zip';
        $zipFilePath = $this->backupPath . '/' . $zipFileName;
        $sqlFileName = 'database-' . $timestamp . '.sql';
        $sqlFilePath = $this->backupPath . '/' . $sqlFileName;

        try {
            // Step 1: DB dump (5-30%)
            $this->writeStatus($backupId, 'processing', 'Menghubungkan ke database...', 6, 'database');
            $result = $this->dumpDatabase($sqlFilePath, $backupId);
            if ($result !== true) {
                $this->writeStatus($backupId, 'failed', $result, 0, 'database');
                return;
            }
            $dbSize = $this->fmtBytes(filesize($sqlFilePath));
            $this->writeStatus($backupId, 'processing', "Database selesai ({$dbSize})", 30, 'database');

            // Step 2: ZIP (30-40%)
            $this->writeStatus($backupId, 'processing', 'Membuat file ZIP...', 35, 'zip');
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                if (file_exists($sqlFilePath)) unlink($sqlFilePath);
                $this->writeStatus($backupId, 'failed', 'Gagal membuat file ZIP', 0, 'zip');
                return;
            }
            $zip->addFile($sqlFilePath, 'database/' . $sqlFileName);
            $this->writeStatus($backupId, 'processing', 'Database ditambahkan ke ZIP', 40, 'zip');

            // Step 3: Storage files (40-90%)
            $storagePath = storage_path('app/public');
            if (File::isDirectory($storagePath)) {
                $this->writeStatus($backupId, 'processing', 'Menghitung file storage...', 42, 'storage');
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($storagePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                $fileList      = iterator_to_array($files);
                $totalFiles    = count($fileList);
                $processed     = 0;
                $totalSize     = 0;

                $this->writeStatus($backupId, 'processing', "Ditemukan {$totalFiles} file storage", 45, 'storage', [
                    'total_files' => $totalFiles, 'processed_files' => 0,
                ]);

                foreach ($fileList as $file) {
                    if (!$file->isDir()) {
                        $fp = $file->getRealPath();
                        $zip->addFile($fp, 'storage/' . substr($fp, strlen($storagePath) + 1));
                        $totalSize += filesize($fp);
                        $processed++;

                        // OPTIMIZATION: Flush zip to disk every 200 files to prevent memory exhaustion
                        // Default ZipArchive menahan semua di RAM sampai close() dipanggil (di 92%)
                        if ($processed % 200 === 0) {
                            $zip->close();
                            $zip->open($zipFilePath);
                        }

                        if ($processed % 10 === 0 || $processed === $totalFiles) {
                            $pct  = 45 + (($processed / max($totalFiles, 1)) * 45);
                            $name = basename($fp);
                            $this->writeStatus($backupId, 'processing', "File: {$name}", round($pct), 'storage', [
                                'total_files'     => $totalFiles,
                                'processed_files' => $processed,
                                'current_file'    => $name,
                                'total_size'      => $this->fmtBytes($totalSize),
                            ]);
                        }
                    }
                }
                $this->writeStatus($backupId, 'processing',
                    "Storage selesai ({$totalFiles} file, " . $this->fmtBytes($totalSize) . ')',
                    90, 'storage', [
                        'total_files'     => $totalFiles,
                        'processed_files' => $totalFiles,
                        'total_size'      => $this->fmtBytes($totalSize),
                    ]);
            }

            // Step 4: Finalisasi
            $this->writeStatus($backupId, 'processing', 'Memfinalisasi backup...', 92, 'finalize');
            $zip->close();
            if (file_exists($sqlFilePath)) unlink($sqlFilePath);

            if (!file_exists($zipFilePath) || filesize($zipFilePath) === 0) {
                $this->writeStatus($backupId, 'failed', 'File backup kosong', 0, 'finalize');
                return;
            }

            $fileSize = $this->fmtBytes(filesize($zipFilePath));
            $this->writeStatus($backupId, 'completed',
                "Backup lengkap berhasil: {$zipFileName} ({$fileSize})", 100, 'done');

        } catch (\Exception $e) {
            if (isset($sqlFilePath) && file_exists($sqlFilePath)) unlink($sqlFilePath);
            if (isset($zipFilePath) && file_exists($zipFilePath)) unlink($zipFilePath);
            $this->writeStatus($backupId, 'failed', 'Error: ' . $e->getMessage(), 0, 'database');
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function dumpDatabase(string $filePath, string $backupId): bool|string
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', '3306');
        $db   = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');

        // Try mysqldump first
        foreach (['mysqldump', '/usr/bin/mysqldump', '/usr/local/bin/mysqldump'] as $bin) {
            if (@file_exists($bin) || $this->commandExists($bin)) {
                $passArg = empty($pass) ? '' : '-p' . escapeshellarg($pass);
                $cmd = sprintf(
                    '%s -h %s -P %s -u %s %s %s > %s 2>&1',
                    escapeshellarg($bin),
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($user),
                    $passArg,
                    escapeshellarg($db),
                    escapeshellarg($filePath)
                );
                exec($cmd, $out, $ret);
                if ($ret === 0 && file_exists($filePath) && filesize($filePath) > 0) {
                    return true;
                }
            }
        }

        // Fallback: PHP PDO dump
        return $this->pdoDump($filePath, $host, $port, $db, $user, $pass, $backupId);
    }

    private function pdoDump(string $filePath, string $host, string $port, string $db, string $user, string $pass, string $backupId): bool|string
    {
        try {
            $pdo = new \PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
                $user, $pass, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

            $out  = "-- Backup: " . date('Y-m-d H:i:s') . "\nSET FOREIGN_KEY_CHECKS=0;\n";
            $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
            $total  = count($tables);

            foreach ($tables as $i => $table) {
                $pct = 8 + (($i / max($total, 1)) * 20);
                $this->writeStatus($backupId, 'processing', "Backup tabel: {$table}", round($pct), 'database', [
                    'total_tables'     => $total,
                    'processed_tables' => $i,
                    'current_table'    => $table,
                ]);

                $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
                $out .= "\nDROP TABLE IF EXISTS `{$table}`;\n" . $create['Create Table'] . ";\n";

                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $vals = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote($v), $row);
                    $out .= "INSERT INTO `{$table}` VALUES (" . implode(',', $vals) . ");\n";
                }
            }
            $out .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
            file_put_contents($filePath, $out);

            return file_exists($filePath) && filesize($filePath) > 0
                ? true
                : 'Gagal menulis file SQL';

        } catch (\Exception $e) {
            return 'PDO dump gagal: ' . $e->getMessage();
        }
    }

    private function commandExists(string $cmd): bool
    {
        $which = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        exec("{$which} {$cmd} 2>&1", $o, $r);
        return $r === 0;
    }

    private function writeStatus(string $backupId, string $status, string $message,
                                  int $progress = 0, string $step = '', array $details = []): void
    {
        $file = storage_path('app/backup_status_' . $backupId . '.json');
        file_put_contents($file, json_encode([
            'status'     => $status,
            'message'    => $message,
            'progress'   => $progress,
            'step'       => $step,
            'details'    => $details,
            'updated_at' => date('Y-m-d H:i:s'),
        ]));
    }

    private function fmtBytes(int $bytes, int $prec = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow   = min(floor(($bytes ? log($bytes) : 0) / log(1024)), count($units) - 1);
        return round($bytes / pow(1024, $pow), $prec) . ' ' . $units[$pow];
    }
}
