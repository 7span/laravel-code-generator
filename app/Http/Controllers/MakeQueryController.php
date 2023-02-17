<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\Help;

class MakeQueryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query_name' => 'required|max:255',
            'query_text' => 'required|max:255',
        ], [
            'query_name.required' => 'Please enter your query name.',
            'query_text.required' => 'Please enter query text.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $generatedFilesPath = Helper::generateBaseDirectory();


        // QuryHelper::makeQueryFile($request->query_name, $generatedFilesPath, implode(',', $methods));

        dd($request->all());


    }
}
