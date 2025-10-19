<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupSqliteDatabase extends Command
{
    
    // protected $signature = 'app:backup-sqlite-database';
    // protected $description = 'Command description';

    protected $signature = 'db:backup-sqlite';
    protected $description = 'Backup the SQLite database file';

    // command: php artisan db:backup-sqlite ==> storage/app/backups/

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dbPath = database_path('database.sqlite');
        $backupDir = storage_path('app/backups');

        if (!file_exists($dbPath)) {
            $this->error("Database file not found at {$dbPath}");
            return;
        }

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $backupFile = $backupDir . '/backup-' . now()->format('Y-m-d_H-i-s') . '.sqlite';

        if (copy($dbPath, $backupFile)) {
            $this->info("✅ SQLite database backed up to: {$backupFile}");
        } else {
            $this->error("❌ Failed to back up database.");
        }
    }
}
