<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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
            'method.required' => 'Please select atleast one method.'
        ]);
 
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $model_name = $this->getModelName($request->get('model_name'));
        $generated_files_path = 'Generated_files_' . date("Y_m_d_His", time());
        $methods = $request->get('method');
        $storage = Storage::disk('local')->exists($generated_files_path);
        
        if ($storage == false) {
            Storage::disk('local')->makeDirectory($generated_files_path);
        }

        $table_name = strtolower(Str::plural(preg_replace('/\B([A-Z])/', '_$1', $model_name)));
        $replaceable_text = $this->getReplaceableText($request->get('table_fields'), $table_name);

        // Make model and move it to Generated_files
        $this->makeModel($model_name, $replaceable_text[2], $generated_files_path);
        
        // Make controller and move it to Generated_files
        $this->makeController($model_name, $generated_files_path);
        
        // Make migration and move it to Generated_files
        $this->makeMigration($table_name, $replaceable_text[0], $generated_files_path);
        
        // Make api-v1.php route file and write content into the file
        $this->makeRouteFiles($model_name, $methods, $generated_files_path, $request->get('admin_crud'));
        
        // Make service file and move it to Generated_files
        $this->makeServiceFile($model_name, $generated_files_path);
        
        // Make resource files and move it to Generated_files
        $this->makeResourceFiles($model_name, $methods, $generated_files_path);
        
        // Make request file and move it to Generated_files
        $this->makeRequestFiles($model_name, $replaceable_text[1], $generated_files_path);
        
        // $filePath = $generated_files_path . '.zip';
        // Get real path for our folder
        $this->makeZip($generated_files_path);

        // Delete the generated folder from the storage
        File::deleteDirectory(storage_path("app/" . $generated_files_path));

        return response()->json(['file_path' => $generated_files_path . '.zip']);
    }

    public function replace_string_in_file($filename, $string_to_replace, $replace_with){
        $content=file_get_contents($filename);
        $content_chunks=explode($string_to_replace, $content);
        $content=implode($replace_with, $content_chunks);
        file_put_contents($filename, $content);
    }

    public function getModelName($string)
    {
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = Str::singular($string);
        $model_name = ucfirst($string);

        return $model_name;
    }

    public function getReplaceableText($table_fields, $table_name)
    {   
        $migration_text = '';
        $rule_text = '';
        $fillable_text = '';

        if ($table_fields != null) {
            foreach($table_fields as $field => $values) {
                $val = get_object_vars(json_decode(str_replace("'", '"', $values)));
                $field_type = $val['type'];
                $validation = $val['validation'];
                $possible_values = $val['possible_values'];
                
                $null_or_not_null = $validation == 'required' ? ' NOT NULL' : ' NULL';
                
                if ($field_type == 'enum') {
                    $p_val = '';
                    $length = count(explode(",", $possible_values));
        
                    foreach (explode(",", $possible_values) as $key => $v) {
                        $p_val .= $length == $key + 1 ? "'" . $v . "'" : "'" . $v . "',";
                    }
    
                    $migration_text .= 'DB::statement("ALTER TABLE ' . $table_name . ' ADD COLUMN ' . $field . ' ENUM(' . $p_val . ')");';
                } else if ($field_type == 'decimal' || $field_type == 'double' || $field_type == 'float') {
                    $val = get_object_vars(json_decode(str_replace("'", '"', $values)));
                    $total_number = $val['total_number'];
                    $decimal_precision = $val['decimal_precision'];

                    $migration_text .= 'DB::statement("ALTER TABLE ' . $table_name . ' ADD COLUMN ' . $field . ' decimal(' . $total_number . ',' . $decimal_precision .')");';
                } else if ($field_type == 'tinyInteger') {
                    $migration_text .= 'DB::statement("ALTER TABLE ' . $table_name . ' ADD COLUMN ' . $field . ' SMALLINT default 0' . ' NOT NULL");';
                } else if ($field_type == 'varchar') {
                    $val = get_object_vars(json_decode(str_replace("'", '"', $values)));
                    $character_limit = $val['character_limit'];

                    $migration_text .= 'DB::statement("ALTER TABLE ' . $table_name . ' ADD COLUMN ' . $field . ' VARCHAR(' . $character_limit . ')' . $null_or_not_null . '");';
                } else if ($field_type == 'mediumText') {
                    $migration_text .= 'DB::statement("ALTER TABLE ' . $table_name . ' ADD COLUMN ' . $field . ' MEDIUMTEXT' . $null_or_not_null . '");';
                } else if ($field_type == 'longText') {
                    $migration_text .= 'DB::statement("ALTER TABLE ' . $table_name . ' ADD COLUMN ' . $field . ' LONGTEXT' . $null_or_not_null . '");';
                } else {
                    $migration_text .= 'DB::statement("ALTER TABLE ' . $table_name . ' ADD COLUMN ' . $field . ' ' . $field_type . $null_or_not_null . ')");';
                }

                if($validation == 'required' && array_key_last($table_fields) != $field) {
                    $rule_text .= '"' . $field . '" => "' . $validation . '",';
                } else if($validation == 'required' && array_key_last($table_fields) == $field) {
                    $rule_text .= '"' . $field . '" => "' . $validation . '"';
                }
                
                $fillable_text .= "'" . $field . "',";
            }
        }

        return [$migration_text, $rule_text, $fillable_text];
    }

    public function makeZip($generated_files_path)
    {
        $rootPath = storage_path("app/" . $generated_files_path);
        
        // Initialize archive object
        $zip = new \ZipArchive();
        $zip->open($generated_files_path . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        
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

    public function makeModel($model_name, $replaceable_text, $generated_files_path)
    {
        \Artisan::call("make:model " . $model_name);
        $filename = base_path("app/Models/".$model_name.".php");
        $string_to_replace="fillable = [];";
        $replace_with = "fillable = [" . $replaceable_text . "];";
        $this->replace_string_in_file($filename, $string_to_replace, $replace_with);
        File::move($filename, storage_path("app/".$generated_files_path."/".$model_name.".php"));
    }

    public function makeController($model_name, $generated_files_path)
    {
        \Artisan::call("make:controller API/V1/" . $model_name . "Controller --api");
        Storage::disk('local')->makeDirectory($generated_files_path . '/Controllers/API/V1');
        File::move(base_path("app/Http/Controllers/API/V1/".$model_name."Controller.php"), storage_path("app/".$generated_files_path."/Controllers/API/V1/".$model_name."Controller.php"));
    }

    public function makeMigration($table_name, $replaceable_text, $generated_files_path)
    {
        \Artisan::call("make:migration create_" . $table_name . "_table");
        $files = scandir(base_path("database/migrations"), SCANDIR_SORT_DESCENDING);
        $newest_file = $files[0];
        $filename = base_path("database/migrations/" . $newest_file);
        $string_to_replace="});";
        $replace_with = "});" . $replaceable_text;
        $this->replace_string_in_file($filename, $string_to_replace, $replace_with);
        File::move(base_path("database/migrations/" . $newest_file), storage_path("app/".$generated_files_path . "/" . $newest_file));
    }

    public function makeRouteFiles($model_name, $methods, $generated_files_path, $admin_crud)
    {
        Storage::disk('local')->put($generated_files_path . '/api-v1.php', file_get_contents(base_path("stubs/api.v1.routes.stub")));
        
        if (count($methods) == 5) {
            $route = "Route::apiResource('" . strtolower($model_name). "s', " . ucfirst($model_name) . "Controller::class);";
        } else {
            $route = "Route::apiResource('" . strtolower($model_name). "s', " . ucfirst($model_name) . "Controller::class)->only(['" . implode("', '", $methods) . "']);";
        }
        Storage::disk('local')->append($generated_files_path . '/api-v1.php', $route, PHP_EOL);
        
        if ($admin_crud == "1") {
            // Make controller and move it to Generated_files
            \Artisan::call("make:controller API/V1/Admin/" . $model_name . "Controller --api");
            Storage::disk('local')->makeDirectory($generated_files_path . '/Controllers/API/V1/Admin');
            File::move(base_path("app/Http/Controllers/API/V1/Admin/".$model_name."Controller.php"), storage_path("app/".$generated_files_path."/Controllers/API/V1/Admin/".$model_name."Controller.php"));
            
            // Make api-admin-v1.php route file and write content into the file
            Storage::disk('local')->put($generated_files_path . '/api-admin-v1.php', file_get_contents(base_path("stubs/api.admin.v1.routes.stub")));
            $route = "Route::apiResource('" . strtolower($model_name). "s', " . "Admin" . "\\" . ucfirst($model_name) . "Controller::class);";
            Storage::disk('local')->append($generated_files_path . '/api-admin-v1.php', $route, PHP_EOL);
        }
    }

    public function makeResourceFiles($model_name, $methods, $generated_files_path)
    {
        \Artisan::call("make:resource " . $model_name);
        Storage::disk('local')->makeDirectory($generated_files_path . '/Resources/' . $model_name);
        File::move(base_path("app/Http/Resources/" . $model_name . "/Resource.php"), storage_path("app/" . $generated_files_path . "/Resources/" . $model_name . "/Resource.php"));
        
        if (in_array('index', $methods)) {
            \Artisan::call("make:collection_resource " . $model_name);
            File::move(base_path("app/Http/Resources/" . $model_name . "/Collection.php"), storage_path("app/" . $generated_files_path . "/Resources/" . $model_name . "/Collection.php"));
        }
        rmdir(base_path("app/Http/Resources/" . $model_name));
    }

    public function makeServiceFile($model_name, $generated_files_path)
    {
        \Artisan::call("make:service " . $model_name);
        Storage::disk('local')->makeDirectory($generated_files_path . '/Services');
        File::move(base_path("app/Services/" . $model_name . "Service.php"), storage_path("app/" . $generated_files_path . "/Services/" . $model_name . "Service.php"));
    }

    public function makeRequestFiles($model_name, $replaceable_text, $generated_files_path)
    {
        \Artisan::call("make:request " . $model_name . "Request");
        Storage::disk('local')->makeDirectory($generated_files_path . '/Requests');
        $request_file_path = base_path("app/Http/Requests/".$model_name."Request.php");
        $string_to_replace="//";
        $replace_with = $replaceable_text;
        $this->replace_string_in_file($request_file_path, $string_to_replace, $replace_with);
        File::move($request_file_path, storage_path("app/" . $generated_files_path . "/Requests/" . $model_name . "Request.php"));
    }
}
