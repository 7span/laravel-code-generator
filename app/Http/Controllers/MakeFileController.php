<?php

namespace App\Http\Controllers;

use File;
use App\Library\ZipHelper;
use Illuminate\Support\Str;
use App\Library\TextHelper;
use Illuminate\Http\Request;
use App\Library\ModelHelper;
use App\Library\RouteHelper;
use App\Library\RequestHelper;
use App\Library\ServiceHelper;
use App\Library\ResourceHelper;
use App\Library\MigrationHelper;
use App\Library\ControllerHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MakeFileController extends Controller
{
    public function makeFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'model_name' => 'required|max:255',
            'method' => 'required|max:255',
        ], [
            'model_name.required' => 'Please enter your model name.',
            'method.required' => 'Please select atleast one method.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        // Get model name
        $modelName = ModelHelper::getModelName($request->get('model_name'));

        // Path for generated files
        $generatedFilesPath = 'Generated_files_' . date('Y_m_d_His', time());

        // Get methods which is selected
        $methods = $request->get('method');

        // Is admin CRUD checked or not
        $adminCrud = $request->get('admin_crud');

        // Is soft delete checked or not
        $softDelete = $request->get('soft_delete');

        // Is scope defined for model
        $scope = $request->get('scope');

        // Check if Generated_files folder exit otherwise create it
        $storage = Storage::disk('local')->exists($generatedFilesPath);
        if ($storage == false) {
            Storage::disk('local')->makeDirectory($generatedFilesPath);
        }

        // Get fields of migrations
        $fields = $request->get('table_fields') != null ? array_reverse($request->get('table_fields')) : [];

        // Get table name
        $tableName = strtolower(Str::plural(preg_replace('/\B([A-Z])/', '_$1', $modelName)));

        // Get replaceable text
        $replaceableText = TextHelper::getReplaceableText($fields, $tableName);

        // Make model and move it to Generated_files
        ModelHelper::makeModel($modelName, $tableName, $replaceableText[2], $generatedFilesPath, $scope, $softDelete);

        // Make controller and move it to Generated_files
        ControllerHelper::makeController($modelName, $generatedFilesPath, $adminCrud, implode(',', $methods));


        // Make migration and move it to Generated_files
        MigrationHelper::makeMigration($tableName, $replaceableText[0], $generatedFilesPath, $softDelete);

        // Make api-v1.php route file and write content into the file
        RouteHelper::makeRouteFiles($modelName, $methods, $generatedFilesPath, $adminCrud);

        // Make service file and move it to Generated_files
        ServiceHelper::makeServiceFile($modelName, $generatedFilesPath, implode(',', $methods));

        // Make resource files and move it to Generated_files
        ResourceHelper::makeResourceFiles($modelName, $methods, $generatedFilesPath);
        
        // Make request file and move it to Generated_files
        RequestHelper::makeRequestFiles($modelName, $replaceableText[1], $generatedFilesPath);

        // Get real path for our folder
        ZipHelper::makeZip($generatedFilesPath);

        // Delete the generated folder from the storage
        File::deleteDirectory(storage_path('app/' . $generatedFilesPath));

        return response()->json(['file_path' => $generatedFilesPath . '.zip']);
    }
}
