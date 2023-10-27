<?php

namespace App\Library;

use File;
use Illuminate\Support\Facades\Storage;

class ServiceHelper
{
    public static function makeServiceFile($modelName, $generatedFilesPath, $methods)
    {
        // Make lang folder into Generated_files and copy lang file into it
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/lang/en');
        File::copy(base_path('lang/en/entity.php'), storage_path('app/' . $generatedFilesPath . '/lang/en/entity.php'));

        // Make service file using command and move it to Generated_files
        \Artisan::call('make:service ' . $modelName . " --methods='" . $methods . "'");
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Services');
        File::move(base_path('app/Services/' . $modelName . 'Service.php'), storage_path('app/' . $generatedFilesPath . '/Services/' . $modelName . 'Service.php'));

        // Delete the Services folder
        File::deleteDirectory(base_path('app/Services'));
    }

    public static function makeQraphqlServiceFile($modelName, $generatedFilesPath)
    {
        // Make lang folder into Generated_files and copy lang file into it
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/lang/en');
        File::copy(base_path('lang/en/entity.php'), storage_path('app/' . $generatedFilesPath . '/lang/en/entity.php'));

        // Make service file using command and move it to Generated_files
        \Artisan::call('make:graphql-service ' . $modelName);
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Services');
        File::move(base_path('app/Services/' . $modelName . 'Service.php'), storage_path('app/' . $generatedFilesPath . '/Services/' . $modelName . 'Service.php'));

        // Delete the Services folder
        File::deleteDirectory(base_path('app/Services'));
    }
}
