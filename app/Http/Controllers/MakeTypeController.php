<?php

namespace App\Http\Controllers;

use File;
use Exception;
use App\Library\ZipHelper;
use Illuminate\Support\Str;
use App\Library\TypeHelper;
use Illuminate\Http\Request;
use App\Library\ModelHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MakeTypeController extends Controller
{
    public function fieldsAndDatatypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_text' => 'required|max:255',
        ], [
            'type_text.required' => 'Please enter type text.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $typeTexts = trim(preg_replace('/\s\s+/', '', $request->get('type_text')));
        $typeTexts = explode(',', $typeTexts);

        $fields = [];
        $dataTypes = [];

        foreach ($typeTexts as $typeText) {
            $splitTypeText = explode(': ', $typeText);
            array_push($fields, $splitTypeText[0]);
            array_push($dataTypes, $splitTypeText[1]);
        }

        return response()->json(['fields' => $fields, 'dataTypes' => $dataTypes]);
    }

    public function store(Request $request)
    {
        $rules = array('type_name' => 'required', 'type_text' => 'required');
        $validator = Validator::make($request->all(), $rules);

        // Validate the input and return correct response
        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }

        try {
            // Path for generated files
            $generatedFilesPath = 'Generated_files_' . date('Y_m_d_His', time());

            // Check if Generated_files folder exit otherwise create it
            $storage = Storage::disk('local')->exists($generatedFilesPath);
            if ($storage == false) {
                Storage::disk('local')->makeDirectory($generatedFilesPath);
            }

            $typeName = $request->get('type_name');
            $typeText = $request->get('type_text');

            // Get model name
            $typeName = TypeHelper::getTypeName($request->get('type_name'));
            $typeTexts = trim(preg_replace('/\s\s+/', '', $request->get('type_text')));
            // }

            if (!str_contains($typeName, 'Input')) {
                $typeName = $typeName . 'Type';
            }

            $typeTexts = explode(',', $typeTexts);

            $fields = [];
            $dataTypes = [];

            foreach ($typeTexts as $typeText) {
                $splitTypeText = explode(': ', $typeText);
                array_push($fields, $splitTypeText[0]);
                array_push($dataTypes, $splitTypeText[1]);
            }


            $modelName = str_replace('Type', '', str_replace('Input', '', $typeName));
            // Get table name
            $tableName = strtolower(Str::plural(preg_replace('/\B([A-Z])/', '_$1', $modelName)));
            $scope = '';
            $softDelete = 1;
            // Make model and move it to Generated_files

            $tempFields = "";
            foreach ($fields as $field) {
                $tempFields .= "'" . $field . "',";
            }

            ModelHelper::makeModel($modelName, $tableName, $tempFields, $generatedFilesPath, $scope, $softDelete, true);

            // Get replaceable text
            $filename = TypeHelper::makeType($typeName, implode(',', $fields), implode(',', $dataTypes));

            // Move the file to Generated_files
            File::move($filename, storage_path('app/' . $generatedFilesPath . '/' . str_replace('Type', '', str_replace('Input', '', $typeName))));

            // Get real path for our folder
            ZipHelper::makeZip($generatedFilesPath);

            // Delete the generated folder from the storage
            File::deleteDirectory(storage_path('app/' . $generatedFilesPath));

            return response()->json(['file_path' => $generatedFilesPath . '.zip']);
        } catch (Exception $e) {
            $format_error = ['format' => "Please Enter valid mutation format."];
            return response()->json($format_error, 422);
        }
    }
}
