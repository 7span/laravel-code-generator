<?php

namespace App\Http\Controllers;

use File;
use Exception;
use App\Library\ZipHelper;
use App\Library\TypeHelper;
use Illuminate\Http\Request;
use App\Library\ServiceHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MakeMutationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folder_name' => 'required|max:255',
            'mutation_name' => 'required',
            'mutation_text' => 'required',
        ], [
            'folder_name.required' => 'Please enter folder name.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        try {
            //code...

            // Path for generated files
            $generatedFilesPath = 'Generated_files_' . date('Y_m_d_His', time());

            // Check if Generated_files folder exit otherwise create it
            $storage = Storage::disk('local')->exists($generatedFilesPath);
            if ($storage == false) {
                Storage::disk('local')->makeDirectory($generatedFilesPath);
            }

            // Get mutation name
            $mutationName = $request->get('mutation_name');
            if (! empty($mutationName)) {
                $mutationName = TypeHelper::getMutationName($mutationName);
            }
            $mutationObj = $request->get('mutation_text');
            $folderName = ucfirst($request->get('folder_name'));

            $fields = [];
            $dataTypes = [];
            $requiredArr = [];
            $aliasArr = [];
            if (! empty($mutationObj)) {
                if (empty($mutationName)) {
                    $mutationName = ucfirst(trim(preg_replace('/\s\s+/', '', $mutationObj)));
                }
                $mutationTexts = TypeHelper::getMutationFields($mutationObj);

                Log::info($mutationTexts);
                $mutationTexts = explode(',', $mutationTexts);

                foreach ($mutationTexts as $typeText) {
                    $splitText = explode(': ', $typeText);
                    $requiredVal = '';
                    if (str_contains($splitText[1], '!')) {
                        $requiredVal = '1';
                    }
                    $dataType = str_replace('!', '', $splitText[1]);

                    $aliasVal = TypeHelper::camelCaseToSnakeCase($splitText[0]);
                    array_push($fields, $splitText[0]);
                    array_push($dataTypes, $dataType);
                    array_push($requiredArr, $requiredVal);
                    array_push($aliasArr, $aliasVal);
                }
            } else {
                $inputName = $request->get('input_name');
                $required = $request->get('input_is_required');
                $type = $request->get('input_type');
                $alias = $request->get('input_alias');
                foreach ($inputName as $key => $val) {
                    array_push($fields, $val);
                    $typeVal = isset($type[$key]) ? $type[$key] : '';
                    $requiredVal = isset($required[$key]) ? $required[$key] : '';
                    $aliasVal = isset($alias[$key]) ? $alias[$key] : '';
                    array_push($dataTypes, $typeVal);
                    array_push($aliasArr, $aliasVal);
                    array_push($requiredArr, $requiredVal);
                }
            }
            $mutationName = $mutationName . 'Mutation';

            $filename = TypeHelper::makeMutation($mutationName, $folderName, implode(',', $fields), implode(',', $dataTypes), implode(',', $requiredArr), implode(',', $aliasArr));
            // Move the file to Generated_files
            File::move($filename, storage_path('app/' . $generatedFilesPath . '/' . $folderName));

            $modelName = str_replace('Mutation', '', $mutationName);
            // Make service file and move it to Generated_files
            ServiceHelper::makeQraphqlServiceFile($modelName, $generatedFilesPath);

            // Get real path for our folder
            ZipHelper::makeZip($generatedFilesPath);

            // Delete the generated folder from the storage
            File::deleteDirectory(storage_path('app/' . $generatedFilesPath));

            return response()->json(['file_path' => $generatedFilesPath . '.zip']);
        } catch (Exception $e) {
            $format_error = ['format' => 'Please Enter valid mutation format.'];

            return response()->json($format_error, 422);
        }
    }
}
