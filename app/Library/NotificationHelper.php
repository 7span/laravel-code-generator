<?php

namespace App\Library;

use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotificationHelper
{
    public static function notification($generatedFilesPath)
    {

        // \Artisan::call('make:laravel-data ' . $modelName);

        \Artisan::call('make:notification');

        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Notifications/');

        // Replace the content of file as per our need

        $requestFilePath = base_path('app/Notifications/' . ucfirst($_REQUEST['class_name']) . '.php');

        $requestFilePath = base_path('app/Notifications');


        File::copyDirectory($requestFilePath, storage_path('app/' . $generatedFilesPath . '/Notifications/'));

        // Delete the laravel data folder

        File::deleteDirectory(base_path('app/Notifications/'));


        // notification language file move
        $requestFilePath = base_path('lang/en');

        Storage::disk('local')->makeDirectory($generatedFilesPath . '/lang/en');
        File::copy(base_path('lang/en/notification.php'), storage_path('app/' . $generatedFilesPath . '/lang/en/notification.php'));
        File::delete(base_path('lang/en/notification.php'));
    }
}
