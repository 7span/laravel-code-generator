<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use App\Library\ZipHelper;
use App\Library\TypeHelper;
use Illuminate\Support\Facades\Storage;

class MakeQueryController extends Controller
{
    public function fieldsAndDatatypes(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        $queryType = $request->get('query_type');
        // Path for generated files
        $generatedFilesPath = 'Generated_files_' . date('Y_m_d_His', time());

        // Check if Generated_files folder exit otherwise create it
        $storage = Storage::disk('local')->exists($generatedFilesPath);
        if ($storage == false) {
            Storage::disk('local')->makeDirectory($generatedFilesPath);
        }

        // Get model name
        $queryName = TypeHelper::getTypeName($request->get('query_name'));

        $queryTexts = trim(preg_replace('/\s\s+/', '', $request->get('query_text')));
        $queryTexts = explode(',', $queryTexts);

        $fields = [];
        $dataTypes = [];

        foreach ($queryTexts as $typeText) {
            $splitTypeText = explode(': ', $typeText);
            array_push($fields, $splitTypeText[0]);
            array_push($dataTypes, $splitTypeText[1]);
        }


        if($queryType == 1){
            // Get replaceable text
            $filename = TypeHelper::makeQueryCollection($queryName, implode(',',$fields),implode(',',$dataTypes));
             // Move the file to Generated_files
            File::move($filename, storage_path('app/' . $generatedFilesPath . '/' .str_replace('CollectionQuery','',$queryName)));

        }else{
            // Get replaceable text
            $filename = TypeHelper::makeQuery($queryName, implode(',',$fields),implode(',',$dataTypes));
            // Move the file to Generated_files
            File::move($filename, storage_path('app/' . $generatedFilesPath . '/' .str_replace('Query','',$queryName)));
        }

        // Get real path for our folder
        ZipHelper::makeZip($generatedFilesPath);

        // Delete the generated folder from the storage
        File::deleteDirectory(storage_path('app/' . $generatedFilesPath));

        return response()->json(['file_path' => $generatedFilesPath . '.zip']);
    }
}
