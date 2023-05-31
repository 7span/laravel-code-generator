<?php

namespace App\Library;

use File;
use Illuminate\Support\Facades\Storage;

class RouteHelper
{
    public static function makeRouteFiles($modelName, $methods, $generatedFilesPath, $adminCrud)
    {
        Storage::disk('local')->put($generatedFilesPath . '/api-v1.php', file_get_contents(base_path('stubs/api.v1.routes.stub')));

        if (count($methods) == 5) {
            $route = "Route::apiResource('" . strtolower($modelName) . "s', " . 'V1' . '\\' . ucfirst($modelName) . 'Controller::class);';
        } else {
            $route = "Route::apiResource('" . strtolower($modelName) . "s', " . 'V1' . '\\' . ucfirst($modelName) . "Controller::class)->only(['" . implode("', '", $methods) . "']);";
        }
        Storage::disk('local')->append($generatedFilesPath . '/api-v1.php', $route, PHP_EOL);

        // If admin CRUD is checked then make api-admin-v1.php route file and write content into the file
        if ($adminCrud == '1') {
            Storage::disk('local')->put($generatedFilesPath . '/api-admin-v1.php', file_get_contents(base_path('stubs/api.admin.v1.routes.stub')));
            $route = "Route::apiResource('" . strtolower($modelName) . "s', " . 'Admin' . '\\' . ucfirst($modelName) . 'Controller::class);';
            Storage::disk('local')->append($generatedFilesPath . '/api-admin-v1.php', $route, PHP_EOL);
        }
    }
}
