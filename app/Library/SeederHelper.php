<?php

namespace App\Library;

use File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SeederHelper
{
    public static function makeSeeder($modelName, $generatedFilesPath)
    {
        // Make seeder using command and move it to Generated_files
        \Artisan::call('make:seeder ' . $modelName . 'Seeder');
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/database/seeders');
        File::move(database_path('seeders/' . $modelName . 'Seeder.php'), storage_path('app/' . $generatedFilesPath . '/database/seeders/' . $modelName . 'Seeder.php'));
    }
}