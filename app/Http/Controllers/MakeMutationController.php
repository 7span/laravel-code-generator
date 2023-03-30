<?php

namespace App\Http\Controllers;

use File;
use App\Library\ZipHelper;
use Illuminate\Support\Str;
use App\Library\TypeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MakeMutationController extends Controller
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

        // Get mutation name
        $mutationName = TypeHelper::getMutationName($request->get('mutation_name'));
        $mutationObj = $request->get('mutation_text');
        $fields = [];
        $dataTypes = [];
        $requiredArr = [];
        $aliasArr = [];
        if(!empty($mutationObj)){
            $mutationObj = explode('{',$mutationObj);
            
            $mutationKeyword = ucfirst(trim($mutationObj[0]));    // if required validation in future
            $mutationObjData = explode('(',$mutationObj[1]);
            
            $mutationName = ucfirst(trim(preg_replace('/\s\s+/', '', $mutationObjData[0])));
            $mutationTexts = TypeHelper::getMutationFields($mutationObjData[1]);

            $mutationTexts = explode(',', $mutationTexts);

            foreach ($mutationTexts as $typeText) {
                $splitText = explode(': ', $typeText);
                $requiredVal = "";
                if(str_contains($splitText[1], '!')){
                    $requiredVal = "1";
                }
                $dataType = str_replace('!','',$splitText[1]);
                array_push($fields, $splitText[0]);
                array_push($dataTypes, $dataType);
                array_push($requiredArr,$requiredVal);
            }
        } else {
            $inputName = $request->get('input_name');
            $required = $request->get('input_is_required');
            $type = $request->get('input_type');
            $alias = $request->get('input_alias');
            foreach($inputName as $key => $val){
                array_push($fields,$val);
                $typeVal = isset($type[$key]) ? $type[$key] : '';
                $requiredVal = isset($required[$key]) ? $required[$key] : '';
                $aliasVal = isset($alias[$key]) ? $alias[$key] : '';
                array_push($dataTypes,$typeVal);
                array_push($aliasArr,$aliasVal);
                array_push($requiredArr,$requiredVal);
            }
        }
            

        $filename = TypeHelper::makeMutation($mutationName, implode(',',$fields),implode(',',$dataTypes),implode(',',$requiredArr),implode(',',$aliasArr));
        // Move the file to Generated_files
        File::move($filename, storage_path('app/' . $generatedFilesPath . '/' . $mutationName . '.php'));

        // Get real path for our folder
        ZipHelper::makeZip($generatedFilesPath);

        // Delete the generated folder from the storage
        File::deleteDirectory(storage_path('app/' . $generatedFilesPath));

        return response()->json(['file_path' => $generatedFilesPath . '.zip']);

        
    }
}
