<?php

namespace App\Library;

use File;
use Illuminate\Support\Str;
use App\Library\TextHelper;
use Illuminate\Support\Facades\Storage;

class TypeHelper
{
    const INDENT = '    ';

    public static function getTypeName($string)
    {
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = Str::singular($string);
        $typeName = ucfirst($string);

        return $typeName;
    }

    public static function getTypeFields($string)
    {
        $string = trim(preg_replace('/\s\s+/', ',', $string));
        $string = ltrim($string, ',');
        $string = rtrim($string, ',}');
        return $string;
    }


    public static function getQueryFields($string)
    {
        $string = trim(preg_replace('/\s\s+/', ',', $string));

        $string = ltrim($string, ',');
        $string = rtrim($string, ',}');
        $string = explode(',)',$string);
        $string = isset($string[0]) ? $string[0] : '';
        return $string;
    }


    public static function getMutationName($string)
    {
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = Str::singular($string);
        $typeName = ucfirst($string);

        return $typeName;
    }

    public static function getMutationFields($string){
        $string = trim(preg_replace('/\s\s+/', ',', $string));

        $string = ltrim($string, ',');
        $string = rtrim($string, ',}');
        $string = explode(',)',$string);
        $string = isset($string[0]) ? $string[0] : '';
        return $string;
    }


    public static function makeType($typeName, $fields, $dataTypes)
    {
        // Make model using command
        \Artisan::call('make:type ' . $typeName . ' --fields='.$fields.' --types='.$dataTypes);

        $filename = base_path('app/GraphQL/Type/' .str_replace('Type','',str_replace('Input','',$typeName)));

        return $filename;
    }

    public static function makeQuery($queryName, $fields, $dataTypes)
    {
        // Make model using command
        \Artisan::call('make:query ' . $queryName . ' --fields='.$fields.' --types='.$dataTypes);
        $filename = base_path('app/GraphQL/Query/' .str_replace('ResourceQuery','',$queryName).'/'.$queryName.'.php');

        return $filename;
    }

    public static function makeMutation($mutationName, $fields, $dataTypes, $required, $alias)
    {
        // Make model using command
        \Artisan::call('make:mutation ' . $mutationName . ' --fields='.$fields.' --types='.$dataTypes.' --required='.$required.' --alias='.$alias);
        $filename = base_path('app/GraphQL/Mutation/' . $mutationName . '.php');

        return $filename;
    }

    public static function makeQueryCollection($queryName, $fields, $dataTypes)
    {
        $fields = str_replace(' ,',',',$fields);
        // Make model using command
        \Artisan::call('make:query-collection ' . $queryName . ' --fields='.$fields.' --types='.$dataTypes);
        $filename = base_path('app/GraphQL/Query/' .str_replace('CollectionQuery','',$queryName));

        return $filename;
    }
}
