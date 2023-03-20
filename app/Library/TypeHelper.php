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

    public static function makeType($typeName, $fields, $dataTypes)
    {
        // Make model using command
        \Artisan::call('make:type ' . $typeName . ' --fields='.$fields.' --types='.$dataTypes);

        $filename = base_path('app/GraphQL/Type/' . $typeName . '.php');

        return $filename;
    }

    public static function makeQuery($queryName, $fields, $dataTypes)
    {
        // Make model using command
        \Artisan::call('make:query ' . $queryName . ' --fields='.$fields.' --types='.$dataTypes);
        $filename = base_path('app/GraphQL/Query/' .str_replace('Query','',$queryName));

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
