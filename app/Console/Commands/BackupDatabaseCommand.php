<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * BackupDatabaseCommand
 *
 * Genera un respaldo SQL completo de la base de datos y lo guarda en storage/backups/.
 * Mantiene los ultimos 7 dias de backups, eliminando los mas antiguos automaticamente.
 * 
 * Uso manual: php artisan db:backup
 * Automatico:  Programado en routes/console.php para ejecutarse a las 3:00 AM diariamente.
 */
class BackupDatabaseCommand extends Command
{
    protected $signature   = 'db:backup {--force : Forzar backup aunque no sea produccion}';
    protected $description = 'Genera un respaldo SQL de la base de datos y guarda los ultimos 7 dias.';

    public function handle(): int
    {
        $env = app()->environment();
        if ($env === 'local' && !$this->option('force')) {
            $this->warn('[Backup] Omitido en entorno local. Usa --force para ejecutar igualmente.');
            return self::SUCCESS;
        }

        $this->info('[Backup] Iniciando respaldo de la base de datos...');

        // --- Configuracion ---
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $timestamp  = now()->format('Y-m-d_H-i-s');
        $filename   = "backup_{$database}_{$timestamp}.sql";
        $backupDir  = storage_path('backups');
        $outputPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        // Crear directorio si no existe
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // --- Ejecutar mysqldump ---
        $passwordFlag = !empty($password) ? "-p" . escapeshellarg($password) : '';
        $command = "mysqldump --host={$host} --port={$port} --user={$username} {$passwordFlag} --ssl-ca=/etc/ssl/certs/ca-certificates.crt --single-transaction --routines --triggers {$database} > " . escapeshellarg($outputPath);

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $this->error("[Backup] Error al ejecutar mysqldump (codigo: {$exitCode}).");
            Log::error('[DB Backup] Fallo el respaldo', ['exit_code' => $exitCode, 'output' => $output]);
            return self::FAILURE;
        }

        $sizeMb = round(filesize($outputPath) / 1024 / 1024, 2);
        $this->info("[Backup] Respaldo guardado: {$filename} ({$sizeMb} MB)");
        Log::info('[DB Backup] Respaldo exitoso', ['file' => $filename, 'size_mb' => $sizeMb]);

        // --- Limpiar backups antiguos (mantener los ultimos 7) ---
        $files = collect(glob($backupDir . '/backup_*.sql'))
            ->sortByDesc(fn($f) => filemtime($f));

        $toDelete = $files->slice(7);
        foreach ($toDelete as $old) {
            unlink($old);
            $this->line("[Backup] Eliminado backup antiguo: " . basename($old));
        }

        $this->info('[Backup] Proceso completado correctamente.');
        return self::SUCCESS;
    }
}
