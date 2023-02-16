<?php

namespace App\Http\Controllers;

use File;
use App\Library\ZipHelper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Library\TypeHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MakeTypeController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_name' => 'required|max:255',
            'type_text' => 'required|max:255',
        ], [
            'type_name.required' => 'Please enter your type name.',
            'type_text.required' => 'Please enter type text.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        dd($request->all());

        // Get model name
        $typeName = ModelHelper::getModelName($request->get('type_name'));

        // Path for generated files
        $generatedFilesPath = 'Generated_files_' . date('Y_m_d_His', time());

        // Check if Generated_files folder exit otherwise create it
        $storage = Storage::disk('local')->exists($generatedFilesPath);
        if ($storage == false) {
            Storage::disk('local')->makeDirectory($generatedFilesPath);
        }

        // Get fields of migrations
        $fields = $request->get('table_fields') != null ? array_reverse($request->get('table_fields')) : [];

        // Get replaceable text
        $replaceableText = TextHelper::getReplaceableText($fields, $tableName);

        // Get real path for our folder
        ZipHelper::makeZip($generatedFilesPath);

        // Delete the generated folder from the storage
        File::deleteDirectory(storage_path('app/' . $generatedFilesPath));

        return response()->json(['file_path' => $generatedFilesPath . '.zip']);
    }
}
