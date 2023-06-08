<?php

namespace App\Library;

use File;
use Illuminate\Support\Facades\Storage;

class ControllerHelper
{
    public static function makeController($modelName, $generatedFilesPath, $adminCrud, $methods, $service = '', $resource = '', $requestFile = '')
    {

        // Make API controller using command and move it to Generated_files
        \Artisan::call('make:controller ' . $modelName . " --methods='" . $methods . "' --service='" . $service . "' --resource='" . $resource . "' --requestFile='" . $requestFile . "'");
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Http/Controllers/API/V1');
        File::move(base_path('app/Http/Controllers/API/V1/' . $modelName . 'Controller.php'), storage_path('app/' . $generatedFilesPath . '/Http/Controllers/API/V1/' . $modelName . 'Controller.php'));

        // If admin CRUD is checked then make admin controller and move it to Generated_files
        if ($adminCrud == '1') {
            \Artisan::call('make:admin-controller ' . $modelName . " --methods='" . $methods . "' --service='" . $service . "' --resource='" . $resource . "' --requestFile='" . $requestFile . "'");
            Storage::disk('local')->makeDirectory($generatedFilesPath . '/Http/Controllers/API/V1/Admin');
            File::move(base_path('app/Http/Controllers/API/V1/Admin/' . $modelName . 'Controller.php'), storage_path('app/' . $generatedFilesPath . '/Http/Controllers/API/V1/Admin/' . $modelName . 'Controller.php'));
        }

        // Delete controller folders
        File::deleteDirectory(base_path('app/Http/Controllers/API/V1'));
        File::deleteDirectory(base_path('app/Http/Controllers/API/V1/Admin'));
    }
}
