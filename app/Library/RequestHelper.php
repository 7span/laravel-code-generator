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
        \Artisan::call('make:request ' . $modelName . 'Request');
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Http/Requests');

        // Replace the content of file as per our need
        $requestFilePath = base_path('app/Http/Requests/' . $modelName . 'Request.php');
        
        if ($ruleText != '') {
            $stringToReplace = '//';
            TextHelper::replaceStringInFile($requestFilePath, $stringToReplace, $ruleText);
        }

        file_put_contents($requestFilePath, "");

        $contentStub = file_get_contents(__DIR__.'/../../stubs/validation.stub');

        file_put_contents($requestFilePath,$contentStub);

        File::move($requestFilePath, storage_path('app/' . $generatedFilesPath . '/Http/Requests/' . $modelName . 'Request.php'));

        // Delete the Requests folder
        File::deleteDirectory(base_path('app/Http/Requests'));
    }
}
