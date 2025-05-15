<?php
require_once __DIR__ . '/init.php';

$date = date('Y-m-d_H-i-s');
$backupFile = __DIR__ . "/storage/backups/pdtbank_backup_$date.sql";
$command = "\"C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe\" -u " . DB_USER . " " . DB_NAME . " > \"$backupFile\"";
exec($command);