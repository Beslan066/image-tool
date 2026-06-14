<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-temp-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистка временных файлов старше 1 часа';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tempPath = storage_path('app/public/temp');
        $deletedCount = 0;

        if (!file_exists($tempPath)) {
            $this->info('Директория temp не существует');
            return Command::SUCCESS;
        }

        $files = glob($tempPath . '/*');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file) && $now - filemtime($file) > 3600) {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
        }

        $this->info("Удалено {$deletedCount} временных файлов");

        return Command::SUCCESS;
    }
}
