<?php

namespace App\Library;

use File;
use Illuminate\Support\Str;
use App\Library\TextHelper;
use Illuminate\Support\Facades\Storage;

class QueryHelper
{
    const INDENT = '    ';
    
    public static function getQueryName($string)
    {
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = Str::singular($string);
        $queryName = ucfirst($string);

        return $queryName;
    }

    public static function makeQuery($queryName, $fields)
    {
        // Make model using command
        \Artisan::call('make:query ' . $queryName . '--fields=ok');

        $filename = base_path('app/GraphQL/Query/' . $queryName . '.php');

        dd($fields);

        // Replace the content table name of file as per our need
        $queryText = '';
        if ($scope != '') {
            $scopeFields = explode(',', $scope);

            foreach ($scopeFields as $key => $scopeField) {
                $newScopeField = str_replace(' ', '', ucwords(str_replace('_', ' ', $scopeField)));
                if ($key == 0) {
                    $queryText .= 'public function scope' . $newScopeField . '($' . 'query, ' . '$' . 'value)' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return  ' . '$' . "query->where('" . $scopeField . "', " . '$' . 'value)' . PHP_EOL . self::INDENT . '}' . PHP_EOL . PHP_EOL;
                } else {
                    $queryText .= self::INDENT . 'public function scope' . $newScopeField . '($' . 'query, ' . '$' . 'value)' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return  ' . '$' . "query->where('" . $scopeField . "', " . '$' . 'value)' . PHP_EOL . self::INDENT . '}' . PHP_EOL . PHP_EOL;
                }

                if (array_key_last($scopeFields) != $key) {
                    $scopedFiltersText .= "'" . $scopeField . "',";
                } else {
                    $scopedFiltersText .= "'" . $scopeField . "'";
                }
            }

            $stringToReplace = 'return [];';
            TextHelper::replaceStringInFile($filename, $stringToReplace, $queryText);
        }

        // Move the file to Generated_files
        File::move($filename, storage_path('app/' . $generatedFilesPath . '/' . $queryName . '.php'));

        // Make folder in Generated_files and copy traits files into it
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Traits');
        File::copy(base_path('app/Traits/BaseModel.php'), storage_path('app/' . $generatedFilesPath . '/Traits/BaseModel.php'));
        File::copy(base_path('app/Traits/BootModel.php'), storage_path('app/' . $generatedFilesPath . '/Traits/BootModel.php'));
    }
}
