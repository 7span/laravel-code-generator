<?php

namespace App\Library;

use File;
use App\Library\TextHelper;
use Illuminate\Support\Facades\Storage;

class LaravelDataHelper
{
    public static function laravelData($modelName, $generatedFilesPath)
    {
        // Make request file using command
        \Artisan::call('make:laravel-data ' . $modelName);


        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Http/Data/');
        
        // Replace the content of file as per our need
        // $requestFilePath = base_path('app/Http/Data/' . ucfirst($modelName) . 'LaravelData.php');
        $requestFilePath = base_path('app/Data/' . ucfirst($modelName) . 'LaravelData.php');


        // if ($ruleText != '') {
        //     $stringToReplace = '//';
        //     trim(preg_replace('/\t+/', '', $ruleText));
        //     TextHelper::replaceStringInFile($requestFilePath, $stringToReplace, $ruleText);
        // }

        // Move request file to Generated_files

        // $requestFilePath = base_path('app/Http/Data');
        $requestFilePath = base_path('app/Data');

        // File::copyDirectory($requestFilePath, storage_path('app/' . $generatedFilesPath . '/Http/Data/'));
        File::copyDirectory($requestFilePath, storage_path('app/' . $generatedFilesPath . '/Data/'));

        // Delete the laravel data folder
      
        File::deleteDirectory(base_path('app/Data/'));

     


    }
}
