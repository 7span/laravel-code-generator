<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use App\Library\ZipHelper;
use App\Library\TypeHelper;
use Illuminate\Support\Facades\Storage;
use App\Library\ServiceHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MakeQueryController extends Controller
{

    // public function store(Request $request)
    // {
    //     $queryType = $request->get('query_type');
    //     // Path for generated files
    //     $generatedFilesPath = 'Generated_files_' . date('Y_m_d_His', time());

    //     // Check if Generated_files folder exit otherwise create it
    //     $storage = Storage::disk('local')->exists($generatedFilesPath);
    //     if ($storage == false) {
    //         Storage::disk('local')->makeDirectory($generatedFilesPath);
    //     }

    //     $queryObj = $request->get('query_obj');
    //     if(!empty($queryObj)){
    //         $queryObj = explode('{',$queryObj);
    //         $queryKeyword = ucfirst(trim($queryObj[0]));    // if future validate syntax
    //         $queryObjData = explode('(',$queryObj[1]);

    //         $queryName = trim(preg_replace('/\s\s+/', '', $queryObjData[0]));
    //         if($queryType == 1){
    //             $queryKeyword = "Collection".ucfirst($queryKeyword);
    //         }
    //         $queryName = $queryName."".$queryKeyword;
    //         $queryTexts = TypeHelper::getQueryFields($queryObjData[1]);
    //     } else {
    //         // Get model name
    //         $queryName = TypeHelper::getTypeName($request->get('query_name'));
    //         $queryTexts = trim(preg_replace('/\s\s+/', '', $request->get('query_text')));
    //     }
    //     $queryTexts = explode(',', $queryTexts);


    //     $fields = [];
    //     $dataTypes = [];

    //     foreach ($queryTexts as $typeText) {
    //         $splitTypeText = explode(': ', $typeText);
    //         array_push($fields, $splitTypeText[0]);
    //         array_push($dataTypes, $splitTypeText[1]);
    //     }


    //     if($queryType == 1){
    //         // Get replaceable text
    //         $filename = TypeHelper::makeQueryCollection($queryName, implode(',',$fields),implode(',',$dataTypes));
    //          // Move the file to Generated_files
    //         File::move($filename, storage_path('app/' . $generatedFilesPath . '/' .str_replace('CollectionQuery','',$queryName)));

    //     }else{
    //         // Get replaceable text
    //         $filename = TypeHelper::makeQuery($queryName, implode(',',$fields),implode(',',$dataTypes));
    //         // Move the file to Generated_files
    //         File::move($filename, storage_path('app/' . $generatedFilesPath . '/' .str_replace('Query','',$queryName)));
    //     }

    //     // Get real path for our folder
    //     ZipHelper::makeZip($generatedFilesPath);

    //     // Delete the generated folder from the storage
    //     File::deleteDirectory(storage_path('app/' . $generatedFilesPath));

    //     return response()->json(['file_path' => $generatedFilesPath . '.zip']);
    // }

    public function store(Request $request)
    {

        // dd($request);
        $rules = array('query_name' => 'required', 'query_text' => 'required');
        $validator = Validator::make($request->all(), $rules);

        // Validate the input and return correct response
        if ($validator->fails()) {
            // dd(3);
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

        // $queryObj = $request->get('query_obj');
        $queryName = $request->get('query_name');
        $queryText = $request->get('query_text');

        // if ($request->get('query_name')) {
        //     $error = ['format' => "Please Enter query name."];
        //     return response()->json($error, 422);
        // }

        // if (empty($queryObj) && empty($queryName)) {
        //     $foramt_error = ['format' => "Please Enter either object or text."];
        //     return response()->json($foramt_error, 422);
        // }


        // if (!empty($queryText) && empty($queryName)) {
        //     $error = ['format' => "Please Enter query name."];
        //     return response()->json($error, 422);
        // }

        // if (!empty($queryText)) {

        //     // if ((!strpos($queryText, '{') || !strpos($queryText, '}'))) {
        //     //     $foramt_error = ['format' => "Opening/Closing curly brackets is missing."];
        //     //     return response()->json($foramt_error, 422);
        //     // }

        //     $queryText = explode('{', $queryText);
        //     $queryTextData = explode('(', $queryText[1]);

        //     $queryKeyword = ucfirst(trim($queryText[0]));
        //     if (empty($queryKeyword) || $queryKeyword != 'Query') {
        //         $foramt_error = ['format' => "Please Enter valid query format."];
        //         return response()->json($foramt_error, 422);
        //     }

        //     if (empty($queryTextData[1])) {
        //         $foramt_error = ['format' => "Please Enter valid query format."];
        //         return response()->json($foramt_error, 422);
        //     }


        //     $queryName = trim(preg_replace('/\s\s+/', '', $queryTextData[0]));
        //     $queryTexts = TypeHelper::getQueryFields($queryTextData[1]);
        // }
        // $pattern = '/^[a-zA-Z0-9_]+:[0-9]+(,[a-zA-Z0-9_]+:[0-9]+)*(\.[a-zA-Z]+:[a-zA-Z0-9_]+)?$/';

        // global $pattern;
        // dd($request->get('query_text'));


        // if (preg_match($pattern, $queryTexts)) {
        //     echo "Valid key-value pairs.";
        // } else {
        //     echo "Invalid key-value pairs.";
        // }

        // die();
        // Get model name
        // $queryName = TypeHelper::getTypeName($request->get('query_name'));
        $queryTexts = trim(preg_replace('/\s\s+/', '', $request->get('query_text')));

        $queryTexts = explode(',', $queryTexts);


        $fields = [];
        $dataTypes = [];


        foreach ($queryTexts as $typeText) {
            // dd($typeText);
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
