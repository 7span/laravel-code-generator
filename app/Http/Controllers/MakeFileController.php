<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MakeFileController extends Controller
{
    const INDENT = '    ';

    public function makeFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'model_name' => 'required|max:255',
            'method' => 'required|max:255',
        ], [
            'model_name.required' => 'Please enter your model name.',
            'method.required' => 'Please select atleast one method.'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        // Get model name
        $modelName = $this->getModelName($request->get('model_name'));

        // Path for generated files
        $generatedFilesPath = 'Generated_files_' . date("Y_m_d_His", time());

        // Get methods which is selected
        $methods = $request->get('method');

        // Is admin CRUD checked or not
        $adminCrud = $request->get('admin_crud');
        
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
        $replaceableText = $this->getReplaceableText($fields, $tableName);

        // Make model and move it to Generated_files
        $this->makeModel($modelName, $tableName, $replaceableText[2], $generatedFilesPath, $scope);

        // Make controller and move it to Generated_files
        $this->makeController($modelName, $generatedFilesPath, $adminCrud, implode(",", $methods));
        
        // Make migration and move it to Generated_files
        $this->makeMigration($tableName, $replaceableText[0], $generatedFilesPath);

        // Make api-v1.php route file and write content into the file
        $this->makeRouteFiles($modelName, $methods, $generatedFilesPath, $adminCrud);

        // Make service file and move it to Generated_files
        $this->makeServiceFile($modelName, $generatedFilesPath, implode(",", $methods));

        // Make resource files and move it to Generated_files
        $this->makeResourceFiles($modelName, $methods, $generatedFilesPath);

        // Make request file and move it to Generated_files
        $this->makeRequestFiles($modelName, $replaceableText[1], $generatedFilesPath);

        // $filePath = $generatedFilesPath . '.zip';
        // Get real path for our folder
        $this->makeZip($generatedFilesPath);

        // Delete the generated folder from the storage
        File::deleteDirectory(storage_path("app/" . $generatedFilesPath));

        return response()->json(['file_path' => $generatedFilesPath . '.zip']);
    }

    // A function to make replaceable text
    public function replaceStringInFile($filename, $stringToReplace, $replaceWith){
        $content = file_get_contents($filename);
        $contentChunks = explode($stringToReplace, $content);
        $content = implode($replaceWith, $contentChunks);
        file_put_contents($filename, $content);
    }

    // Get model name from the string
    public function getModelName($string)
    {
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = Str::singular($string);
        $modelName = ucfirst($string);

        return $modelName;
    }

    public function getReplaceableText($tableFields, $tableName)
    {
        // Make replaceable text for model fillable, migration field text and request rule text
        $migrationText = '';
        $ruleText = '';
        $fillableText = '';

        if ($tableFields != null) {
            foreach($tableFields as $field => $values) {
                $val = get_object_vars(json_decode(str_replace("'", '"', $values)));
                $fieldType = $val['type'];
                $validation = $val['validation'];
                $possibleValues = $val['possible_values'];

                $null_or_not_null = $validation == 'required' ? ' NOT NULL' : ' NULL';

                if ($fieldType == 'enum') {
                    $pVal = '';
                    $length = count(explode(",", $possibleValues));

                    foreach (explode(",", $possibleValues) as $key => $v) {
                        if ($key == 0) {
                            $first = $v;
                        }
                        $pVal .= $length == $key + 1 ? "'" . $v . "'" : "'" . $v . "',";
                    }
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->' . $fieldType . '("' . $field . '" , [' . $pVal . '])->default("' . $first . '");';
                } else if ($fieldType == 'decimal' || $fieldType == 'double' || $fieldType == 'float') {
                    $val = get_object_vars(json_decode(str_replace("'", '"', $values)));
                    $totalNumber = $val['total_number'];
                    $decimalPrecision = $val['decimal_precision'];
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->' . $fieldType . '("' . $field . '", ' . $totalNumber . ', ' . $decimalPrecision . ');';
                } else if ($fieldType == 'tinyInteger') {
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->' . $fieldType . '("' . $field . '")->default("0");';
                } else if ($fieldType == 'string') {
                    $val = get_object_vars(json_decode(str_replace("'", '"', $values)));
                    $characterLimit = $val['character_limit'];
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->string("' . $field . '", ' . $characterLimit . ');';
                } else {
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->' . $fieldType . '("' . $field . '");';
                }

                if($validation == 'required' && array_key_last($tableFields) != $field) {
                    $ruleText .= '"' . $field . '" => "' . $validation . '",' . PHP_EOL ;
                } else if($validation == 'required' && array_key_last($tableFields) == $field) {
                    $ruleText .= self::INDENT . self::INDENT . self::INDENT . '"' . $field . '" => "' . $validation . '"';
                }

                $fillableText .= PHP_EOL.self::INDENT . self::INDENT . "'" . $field . "'," ;
            }
        }
        return [$migrationText, $ruleText, $fillableText];
    }

    public function makeZip($generatedFilesPath)
    {
        // Path for the Generated_files
        $rootPath = storage_path("app/" . $generatedFilesPath);

        // Initialize archive object
        $zip = new \ZipArchive();
        $zip->open($generatedFilesPath . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
    }

    public function makeModel($modelName, $tableName, $fillableText, $generatedFilesPath, $scope)
    {
        // Make model using command
        \Artisan::call("make:model " . $modelName);

        // Replace the content of file as per our need
        $filename = base_path("app/Models/".$modelName.".php");
        $stringToReplace="fillable = [";
        $replaceWith = "fillable = [" . $fillableText;
        $this->replaceStringInFile($filename, $stringToReplace, $replaceWith);
        
        // Replace the content table name of file as per our need
        $tableText = "table = '" . $tableName . "'";
        $this->replaceStringInFile($filename, "table = ''", $tableText);

        // Replace the content scopedFilters of file as per our need
        $scopedFiltersText = '';
        
        // Replace the content scopes of file as per our need
        $scopesText = '';
        if ($scope != "") {
            $scopeFields = explode(",", $scope);

            foreach ($scopeFields as $key => $scopeField) {
                $newScopeField = str_replace(" ", "", ucwords(str_replace("_", " ", $scopeField)));
                if ($key == 0) {
                    $scopesText .= "public function scope" . $newScopeField . "($" . "query, " . "$" . "value)" . PHP_EOL . self::INDENT . "{" . PHP_EOL . self::INDENT . self::INDENT . "return  " . "$" . "query->where('" . $scopeField . "', " . "$" . "value)" . PHP_EOL . self::INDENT . "}" . PHP_EOL . PHP_EOL;
                } else {
                    $scopesText .= self::INDENT . "public function scope" . $newScopeField . "($" . "query, " . "$" . "value)" . PHP_EOL . self::INDENT . "{" . PHP_EOL . self::INDENT . self::INDENT . "return  " . "$" . "query->where('" . $scopeField . "', " . "$" . "value)" . PHP_EOL . self::INDENT . "}" . PHP_EOL . PHP_EOL;
                }

                if (array_key_last($scopeFields) != $key) {
                    $scopedFiltersText .= "'" . $scopeField . "',";
                } else {
                    $scopedFiltersText .= "'" . $scopeField . "'";
                }
            }

            $stringToReplace = "scopedFilters = [";
            $replaceWith = $stringToReplace . $scopedFiltersText;
            $this->replaceStringInFile($filename, $stringToReplace, $replaceWith);

            $stringToReplace = "{{ scopes }}";
            $this->replaceStringInFile($filename, $stringToReplace, $scopesText);
        }
        
        // Move the file to Generated_files
        File::move($filename, storage_path("app/".$generatedFilesPath."/".$modelName.".php"));
        
        // Make folder in Generated_files and copy traits files into it
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Traits');
        File::copy(base_path("app/Traits/BaseModel.php"), storage_path("app/".$generatedFilesPath."/Traits/BaseModel.php"));
        File::copy(base_path("app/Traits/BootModel.php"), storage_path("app/".$generatedFilesPath."/Traits/BootModel.php"));
    }

    public function makeController($modelName, $generatedFilesPath, $adminCrud, $methods)
    {
        // Copy traits files into Generated_files
        File::copy(base_path("app/Traits/ApiResponser.php"), storage_path("app/".$generatedFilesPath."/Traits/ApiResponser.php"));

        // Make API controller using command and move it to Generated_files
        \Artisan::call("make:controller " . $modelName . " --methods='" . $methods . "'");
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Http/Controllers/API/V1');
        File::move(base_path("app/Http/Controllers/API/V1/".$modelName."Controller.php"), storage_path("app/".$generatedFilesPath . "/Http/Controllers/API/V1/" . $modelName."Controller.php"));

        // If admin CRUD is checked then make admin controller and move it to Generated_files
        if ($adminCrud == "1") {
            \Artisan::call("make:admin-controller " . $modelName . " --methods='" . $methods . "'");
            Storage::disk('local')->makeDirectory($generatedFilesPath . '/Http/Controllers/API/V1/Admin');
            File::move(base_path("app/Http/Controllers/API/V1/Admin/".$modelName."Controller.php"), storage_path("app/".$generatedFilesPath."/Http/Controllers/API/V1/Admin/".$modelName."Controller.php"));
        }

        // Delete controller folders
        File::deleteDirectory(base_path("app/Http/Controllers/API/V1"));
        File::deleteDirectory(base_path("app/Http/Controllers/API/V1/Admin"));
    }

    public function makeMigration($tableName, $migrationText, $generatedFilesPath)
    {
        // Make migration file using command
        \Artisan::call("make:migration create_" . $tableName . "_table");

        // Replace the content of file as per our need
        $files = scandir(base_path("database/migrations"), SCANDIR_SORT_DESCENDING);
        $newest_file = $files[0];
        $filename = base_path("database/migrations/" . $newest_file);
        $stringToReplace="table->id();";
        $replaceWith = "table->id();" . $migrationText;
        $this->replaceStringInFile($filename, $stringToReplace, str_replace('"', "'", $replaceWith));

        // Move migration file to Generated_files
        File::move(base_path("database/migrations/" . $newest_file), storage_path("app/".$generatedFilesPath . "/" . $newest_file));
    }

    public function makeRouteFiles($modelName, $methods, $generatedFilesPath, $adminCrud)
    {
        Storage::disk('local')->put($generatedFilesPath . '/api-v1.php', file_get_contents(base_path("stubs/api.v1.routes.stub")));

        if (count($methods) == 5) {
            $route = "Route::apiResource('" . strtolower($modelName). "s', " . ucfirst($modelName) . "Controller::class);";
        } else {
            $route = "Route::apiResource('" . strtolower($modelName). "s', " . ucfirst($modelName) . "Controller::class)->only(['" . implode("', '", $methods) . "']);";
        }
        Storage::disk('local')->append($generatedFilesPath . '/api-v1.php', $route, PHP_EOL);

        // If admin CRUD is checked then make api-admin-v1.php route file and write content into the file
        if ($adminCrud == "1") {
            Storage::disk('local')->put($generatedFilesPath . '/api-admin-v1.php', file_get_contents(base_path("stubs/api.admin.v1.routes.stub")));
            $route = "Route::apiResource('" . strtolower($modelName). "s', " . "Admin" . "\\" . ucfirst($modelName) . "Controller::class);";
            Storage::disk('local')->append($generatedFilesPath . '/api-admin-v1.php', $route, PHP_EOL);
        }
    }

    public function makeResourceFiles($modelName, $methods, $generatedFilesPath)
    {
        // Copy traits files into Generated_files
        File::copy(base_path("app/Traits/ResourceFilterable.php"), storage_path("app/".$generatedFilesPath."/Traits/ResourceFilterable.php"));

        // Make resource file using command and move it to Generated_files
        \Artisan::call("make:resource " . $modelName);
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Http/Resources/' . $modelName);
        File::move(base_path("app/Http/Resources/" . $modelName . "/Resource.php"), storage_path("app/" . $generatedFilesPath . "/Http/Resources/" . $modelName . "/Resource.php"));

        // If index method is checked then make collection resource file and move it to Generated_files
        if (in_array('index', $methods)) {
            \Artisan::call("make:collection_resource " . $modelName);
            File::move(base_path("app/Http/Resources/" . $modelName . "/Collection.php"), storage_path("app/" . $generatedFilesPath . "/Http/Resources/" . $modelName . "/Collection.php"));
        }

        // Delete Resources folder
        File::deleteDirectory(base_path("app/Http/Resources"));
    }

    public function makeServiceFile($modelName, $generatedFilesPath, $methods)
    {
        // Copy traits files into Generated_files
        File::copy(base_path("app/Traits/PaginationTrait.php"), storage_path("app/".$generatedFilesPath."/Traits/PaginationTrait.php"));

        // Make lang folder into Generated_files and copy lang file into it
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/lang/en');
        File::copy(base_path("lang/en/entity.php"), storage_path("app/".$generatedFilesPath."/lang/en/entity.php"));

        // Make service file using command and move it to Generated_files
        \Artisan::call("make:service " . $modelName . " --methods='" . $methods . "'");
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Services');
        File::move(base_path("app/Services/" . $modelName . "Service.php"), storage_path("app/" . $generatedFilesPath . "/Services/" . $modelName . "Service.php"));

        // Delete the Services folder
        File::deleteDirectory(base_path("app/Services"));
    }

    public function makeRequestFiles($modelName, $ruleText, $generatedFilesPath)
    {
        // Make request file using command
        \Artisan::call("make:request " . $modelName . "Request");
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Http/Requests');

        // Replace the content of file as per our need
        $requestFilePath = base_path("app/Http/Requests/".$modelName."Request.php");
        if ($ruleText != "") {
            $stringToReplace="//";
            $this->replaceStringInFile($requestFilePath, $stringToReplace, $ruleText);
        }

        // Move request file to Generated_files
        File::move($requestFilePath, storage_path("app/" . $generatedFilesPath . "/Http/Requests/" . $modelName . "Request.php"));
        
        // Delete the Requests folder
        File::deleteDirectory(base_path("app/Http/Requests"));
    }
}
