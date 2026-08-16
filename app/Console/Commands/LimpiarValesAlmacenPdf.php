<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class LimpiarValesAlmacenPdf extends Command
{
    protected $signature = 'vales-almacen:limpiar-pdfs';

    protected $description = 'Eliminar los PDF vencidos de los vales de almacén';

    private FilesystemAdapter $disk;

    private string $folder = 'vales-almacen';

    private int $retentionDays;

    public function __construct()
    {
        parent::__construct();

        $this->disk = Storage::disk('public');
        $this->retentionDays = max(
            1,
            (int) config('vales-almacen.pdf_retention_days', 15)
        );
    }

    public function handle(): int
    {
        $result = $this->deleteExpired();

        $this->info("Retención configurada: {$result['retention_days']} días.");
        $this->line("Eliminados: {$result['deleted']}; vigentes: {$result['current']}; ignorados: {$result['ignored']}; errores: {$result['errors']}.");

        return $result['errors'] === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @return array{deleted: int, current: int, ignored: int, errors: int, retention_days: int}
     */
    private function deleteExpired(): array
    {
        $expirationTimestamp = now()->subDays($this->retentionDays)->timestamp;
        $deleted = 0;
        $current = 0;
        $ignored = 0;
        $errors = 0;

        foreach ($this->disk->files($this->folder) as $filePath) {
            if (! preg_match('/^vale-\d+\.pdf$/i', basename($filePath))) {
                $ignored++;

                continue;
            }

            try {
                if ($this->disk->lastModified($filePath) > $expirationTimestamp) {
                    $current++;

                    continue;
                }

                if ($this->disk->delete($filePath)) {
                    $deleted++;
                } else {
                    $errors++;
                }
            } catch (\Throwable $exception) {
                report($exception);
                $errors++;
            }
        }

        return [
            'deleted' => $deleted,
            'current' => $current,
            'ignored' => $ignored,
            'errors' => $errors,
            'retention_days' => $this->retentionDays,
        ];
    }
}
