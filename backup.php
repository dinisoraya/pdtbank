<?php
require_once __DIR__ . '/init.php';

$date = date('Y-m-d_H-i-s');
$backupFile = __DIR__ . "/storage/backups/pdtbank_backup_$date.sql";
$command = "\"C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe\" -u " . DB_USER . " " . DB_NAME . " > \"$backupFile\"";
exec($command);

if (file_exists($backupFile)) {
    echo "Backup successfully saved to: $backupFile\n";
} else {
    echo "Backup failed.\n";
}

// Setup Task Scheduler Windows
// Untuk menjalankan backup otomatis setiap hari, kita akan menggunakan Task Scheduler di Windows.
// Berikut adalah langkah-langkah untuk mengatur Task Scheduler:

// 1. Buka Task Scheduler
// Cari "Task Scheduler" di Start Menu

// 2. Buat Task Baru
// Klik "Create a Basic Task" di panel kanan
// Name: pdtbank_backup
// Description: Backup otomatis database pdtbank setiap hari

// 3. Atur Trigger
// Pilih "Daily" untuk backup harian
// Set waktu backup
// Pilih "Recur every: 1 days"

// 4. Atur Action
// Pilih "Start a program"
// Program/script: C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe
// Add Arguments (optional): backup.php
// Start in (optional): C:\laragon\www\pdtbank\ 

// 5. Finish
// Klik "Finish" untuk menyelesaikan pembuatan task



