<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use App\Library\ZipHelper;
use App\Library\TypeHelper;
use Illuminate\Support\Facades\Storage;

class MakeTypeController extends Controller
{
    public function fieldsAndDatatypes(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        // Path for generated files
        $generatedFilesPath = 'Generated_files_' . date('Y_m_d_His', time());

        // Check if Generated_files folder exit otherwise create it
        $storage = Storage::disk('local')->exists($generatedFilesPath);
        if ($storage == false) {
            Storage::disk('local')->makeDirectory($generatedFilesPath);
        }

        // Get model name
        $mutationName = TypeHelper::getMutationName($request->get('mutation_name'));
    }
}
