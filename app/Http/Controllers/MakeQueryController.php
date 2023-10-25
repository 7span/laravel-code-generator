<?php

namespace App\Http\Controllers;

use File;
use App\Library\ZipHelper;
use App\Library\TypeHelper;
use Illuminate\Http\Request;
use App\Library\ServiceHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MakeQueryController extends Controller
{
    public function store(Request $request)
    {
        $rules = array('query_name' => 'required', 'query_text' => 'required');
        $validator = Validator::make($request->all(), $rules);

        // Validate the input and return correct response
        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }


        // Path for generated files
        $generatedFilesPath = 'Generated_files_' . date('Y_m_d_His', time());

        // Check if Generated_files folder exit otherwise create it
        $storage = Storage::disk('local')->exists($generatedFilesPath);
        if ($storage == false) {
            Storage::disk('local')->makeDirectory($generatedFilesPath);
        }

        $queryName = $request->get('query_name');
        $queryText = $request->get('query_text');

        $queryTexts = trim(preg_replace('/\s\s+/', '', $request->get('query_text')));

        $queryTexts = explode(',', $queryTexts);


        $fields = [];
        $dataTypes = [];


        foreach ($queryTexts as $typeText) {
            $splitTypeText = explode(': ', $typeText);
            Log::info($splitTypeText);
            array_push($fields, $splitTypeText[0] ?? '');
            array_push($dataTypes, $splitTypeText[1] ?? '');
        }

        $collectionQueryName = $queryName . 'CollectionQuery';

        // Get replaceable text
        $filename = TypeHelper::makeQueryCollection($collectionQueryName, implode(',', $fields), implode(',', $dataTypes));
        // Move the file to Generated_files
        File::move($filename, storage_path('app/' . $generatedFilesPath . '/' . str_replace('CollectionQuery', '', $collectionQueryName)));


        $queryName = $queryName . 'ResourceQuery';

        // Get replaceable text
        $filename = TypeHelper::makeQuery($queryName, implode(',', $fields), implode(',', $dataTypes));

        // Move the file to Generated_files
        File::move($filename, storage_path('app/' . $generatedFilesPath . '/' . str_replace('ResourceQuery', '', $queryName)) . '/' . $queryName . '.php');


        $modelName = str_replace('CollectionQuery', '', str_replace('ResourceQuery', '', $queryName));

        // Make service file and move it to Generated_files
        ServiceHelper::makeQraphqlServiceFile($modelName, $generatedFilesPath);

        File::copyDirectory(base_path('app/Traits/'), storage_path('app/' . $generatedFilesPath . '/Traits'));

        // Get real path for our folder
        ZipHelper::makeZip($generatedFilesPath);

        // Delete the generated folder from the storage
        File::deleteDirectory(storage_path('app/' . $generatedFilesPath));

        return response()->json(['file_path' => $generatedFilesPath . '.zip']);
    }
}
