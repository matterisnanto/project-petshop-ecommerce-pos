<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class DeleteEmptyDirectories extends Command
{
    protected $signature = 'delete:empty-dirs {path?}';
    protected $description = 'Hapus semua folder kosong di direktori tertentu';

    public function handle(Filesystem $filesystem)
    {
        $path = $this->argument('path') ?? base_path();

        // Fungsi rekursif untuk menghapus direktori kosong
        $deleteEmptyDirs = function ($dir) use (&$deleteEmptyDirs, $filesystem) {
            foreach ($filesystem->directories($dir) as $subdir) {
                $deleteEmptyDirs($subdir); // Rekursif ke subdirektori
            }

            // Hapus direktori jika kosong (tidak ada file)
            if (count($filesystem->allFiles($dir)) === 0) {
                $filesystem->deleteDirectory($dir);
                $this->info("Folder {$dir} berhasil dihapus");
            }
        };

        $deleteEmptyDirs($path);
    }
}

// php artisan delete:empty-dirs