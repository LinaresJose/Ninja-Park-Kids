<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza un volcado (backup) de la base de datos local usando mysqldump y elimina backups antiguos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d_H-i-s') . ".sql";
        $storagePath = storage_path('app/backups');

        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        $filePath = $storagePath . DIRECTORY_SEPARATOR . $filename;

        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        // Intentar encontrar mysqldump en Laragon si existe
        $mysqldumpPath = 'mysqldump';
        $laragonMysqlPath = 'C:\laragon\bin\mysql';
        if (File::exists($laragonMysqlPath)) {
            $directories = File::directories($laragonMysqlPath);
            if (!empty($directories)) {
                $mysqldumpPath = $directories[0] . '\bin\mysqldump.exe';
            }
        }

        $passwordParam = $dbPass ? "-p\"{$dbPass}\"" : "";

        // En Windows puede que necesite comillas, pero en exec() se pasa directo.
        $command = "\"{$mysqldumpPath}\" -h {$dbHost} -P {$dbPort} -u {$dbUser} {$passwordParam} {$dbName} > \"{$filePath}\"";

        $returnVar = null;
        $output = null;

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Backup creado exitosamente: {$filePath}");
            Log::info("Database backup created successfully", ['file' => $filename]);
            
            // Limpiar backups antiguos (retención de 7 días)
            $this->cleanOldBackups($storagePath, 7);
        } else {
            $this->error("Error al crear el backup de la base de datos.");
            Log::error("Database backup failed", ['command' => $command, 'return_var' => $returnVar]);
        }
    }

    private function cleanOldBackups($directory, $retentionDays)
    {
        $files = File::files($directory);
        $now = Carbon::now();
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp($file->getMTime());

            if ($lastModified->diffInDays($now) > $retentionDays) {
                File::delete($file->getRealPath());
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("Se eliminaron {$deletedCount} backups antiguos (más de {$retentionDays} días).");
            Log::info("Old backups cleaned up", ['deleted_count' => $deletedCount]);
        }
    }
}
