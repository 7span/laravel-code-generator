<?php

namespace App\Library;

use File;
use App\Library\TextHelper;
use Illuminate\Support\Facades\Storage;

class RequestHelper
{
    public static function makeRequestFiles($modelName, $ruleText, $generatedFilesPath)
    {
        // Make request file using command
        \Artisan::call('make:request ' . ucfirst($modelName) .'/'.'Request');
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Http/Requests/'.ucfirst($modelName));

        // Replace the content of file as per our need
        $requestFilePath = base_path('app/Http/Requests/' .ucfirst($modelName).'/Request.php');

        if ($ruleText != '') {
            $stringToReplace = '//';
            trim(preg_replace('/\t+/', '', $ruleText));
            TextHelper::replaceStringInFile($requestFilePath, $stringToReplace, $ruleText);
        }

        // Move request file to Generated_files
        $requestFilePath = base_path('app/Http/Requests/' .ucfirst($modelName));
        
        File::copyDirectory($requestFilePath, storage_path('app/' . $generatedFilesPath . '/Http/Requests/' .ucfirst($modelName)));

        // Delete the Requests folder
        File::deleteDirectory(base_path('app/Http/Requests/').ucfirst($modelName));
    }
}
