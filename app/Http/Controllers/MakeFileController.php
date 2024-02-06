<?php

namespace App\Http\Controllers;

use File;
use App\Library\ZipHelper;
use App\Library\TextHelper;
use Illuminate\Support\Str;
use App\Library\ModelHelper;
use App\Library\RouteHelper;
use Illuminate\Http\Request;
use App\Library\RequestHelper;
use App\Library\ServiceHelper;
use App\Library\ResourceHelper;
use App\Library\MigrationHelper;
use App\Library\ControllerHelper;
use App\Library\NotificationHelper;
use App\Library\SeederHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MakeFileController extends Controller
{
    public function store(Request $request)
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
        $generatedFilesPath = $modelName . '_' . date('Y_m_d_His', time());

        // Get methods which is selected
        $methods = $request->get('method');

        // Is admin CRUD checked or not
        $adminCrud = $request->get('admin_crud');

        // Is soft delete checked or not
        $softDelete = $request->get('soft_delete');

        // Is scope defined for model
        $scope = $request->get('scope');

        $trait = $request->get('trait');

        $deletedBy = $request->get('deleted_by');

        $service = $request->get('service');
        $resource = $request->get('resource');
        $requestFile = $request->get('request');
        $notification = $request->get('notification');
        $includeSeeder = $request->get('seeder');

        $relationModel = $request->get('relation_model');
        $relationShip = $request->get('relation_ship');
        $relationAnotherModel = $request->get('relation_another_model');
        $foreignKey = $request->get('foreign_key');
        $localKey = $request->get('local_key');

        $relationArr = [
            'relationShip' => $relationShip,
            'relationModel' => $relationModel,
            'relationAnotherModel' => $relationAnotherModel,
            'foreignKey' => $foreignKey,
            'localKey' => $localKey,
        ];

        $includeModel = $request->get('add_model');
        $includeMigration = $request->get('add_migration');

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

        if ($includeModel == 1) {
            // Make model and move it to Generated_files
            ModelHelper::makeModel($modelName, $tableName, $replaceableText[2], $generatedFilesPath, $scope, $softDelete, $deletedBy, $trait, $relationArr);
        }
        // Make controller and move it to Generated_files
        ControllerHelper::makeController($modelName, $generatedFilesPath, $adminCrud, implode(',', $methods), $service, $resource, $requestFile);

        if ($trait == 1) {
            // Make folder in Generated_files and copy traits files into it
            Storage::disk('local')->makeDirectory($generatedFilesPath . '/Traits');

            File::copyDirectory(base_path('app/Traits/'), storage_path('app/' . $generatedFilesPath . '/Traits'));
        }

        if ($includeMigration == 1) {
            // Make migration and move it to Generated_files
            MigrationHelper::makeMigration($tableName, $replaceableText[0], $generatedFilesPath, $softDelete, $deletedBy);
        }
        if ($includeSeeder == 1) {
            // Make seeder and move it to Generated_files
            SeederHelper::makeSeeder($modelName, $generatedFilesPath);
        }
        // Make api-v1.php route file and write content into the file
        RouteHelper::makeRouteFiles($modelName, $methods, $generatedFilesPath, $adminCrud);

        if ($service == 1) {
            // Make service file and move it to Generated_files
            ServiceHelper::makeServiceFile($modelName, $generatedFilesPath, implode(',', $methods));
        }

        if ($resource == 1) {
            // Make resource files and move it to Generated_files
            ResourceHelper::makeResourceFiles($modelName, $methods, $generatedFilesPath);
        }

        if ($requestFile == 1) {
            // Make request file and move it to Generated_files
            RequestHelper::makeRequestFiles($modelName, $replaceableText[1], $generatedFilesPath);
        }

        if ($notification == 1) {
            if (empty($request->class_name)) {
                $classNameError = ['format' => 'Please Enter valid mutation format.'];

                return response()->json($classNameError, 422);
            }
            $titleKey = $this->camelCaseToUnderscore($request->class_name);
            $bodyKey = $this->camelCaseToUnderscore($request->class_name) . '_body';

            $titleValue = $request->subject;
            $bodyValue = $request->body;

            $command = 'make:language ' . "'" . $titleKey . "'" . " '" . $titleValue . "'" . " '" . $bodyKey . "'" . " '" . $bodyValue . "'";

            \Artisan::call($command);
            NotificationHelper::notification($generatedFilesPath);
        }

        // Get real path for our folder
        ZipHelper::makeZip($generatedFilesPath);

        // Delete the generated folder from the storage
        File::deleteDirectory(storage_path('app/' . $generatedFilesPath));

        return response()->json(['file_path' => $generatedFilesPath . '.zip']);
    }

    public function camelCaseToUnderscore($input)
    {
        // Use a regular expression to match the CamelCase pattern
        $pattern = '/(?!^)([A-Z])/';
        $replacement = '_$1';
        $underscored = preg_replace($pattern, $replacement, $input);

        // Convert to lowercase
        return strtolower($underscored);
    }
}