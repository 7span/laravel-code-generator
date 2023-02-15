<?php

namespace App\Library;

use File;
use Illuminate\Support\Facades\Storage;

class ResourceHelper
{
    public static function makeResourceFiles($modelName, $methods, $generatedFilesPath)
    {
        // Copy traits files into Generated_files
        File::copy(base_path('app/Traits/ResourceFilterable.php'), storage_path('app/' . $generatedFilesPath . '/Traits/ResourceFilterable.php'));

        // Make resource file using command and move it to Generated_files
        \Artisan::call('make:resource ' . $modelName);
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Http/Resources/' . $modelName);
        File::move(base_path('app/Http/Resources/' . $modelName . '/Resource.php'), storage_path('app/' . $generatedFilesPath . '/Http/Resources/' . $modelName . '/Resource.php'));

        // If index method is checked then make collection resource file and move it to Generated_files
        if (in_array('index', $methods)) {
            \Artisan::call('make:collection_resource ' . $modelName);
            File::move(base_path('app/Http/Resources/' . $modelName . '/Collection.php'), storage_path('app/' . $generatedFilesPath . '/Http/Resources/' . $modelName . '/Collection.php'));
        }

        // Delete Resources folder
        File::deleteDirectory(base_path('app/Http/Resources'));
    }
}
