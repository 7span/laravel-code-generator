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

    public static function makeType($typeName, $tableName, $fillableText, $generatedFilesPath, $scope, $softDelete)
    {
        // Make model using command
        \Artisan::call('make:type ' . $typeName);

        $filename = base_path('app/GraphQL/Type/' . $typeName . '.php');

        // Replace the content table name of file as per our need
        $typeText = '';
        if ($scope != '') {
            $scopeFields = explode(',', $scope);

            foreach ($scopeFields as $key => $scopeField) {
                $newScopeField = str_replace(' ', '', ucwords(str_replace('_', ' ', $scopeField)));
                if ($key == 0) {
                    $typeText .= 'public function scope' . $newScopeField . '($' . 'query, ' . '$' . 'value)' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return  ' . '$' . "query->where('" . $scopeField . "', " . '$' . 'value)' . PHP_EOL . self::INDENT . '}' . PHP_EOL . PHP_EOL;
                } else {
                    $typeText .= self::INDENT . 'public function scope' . $newScopeField . '($' . 'query, ' . '$' . 'value)' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return  ' . '$' . "query->where('" . $scopeField . "', " . '$' . 'value)' . PHP_EOL . self::INDENT . '}' . PHP_EOL . PHP_EOL;
                }

                if (array_key_last($scopeFields) != $key) {
                    $scopedFiltersText .= "'" . $scopeField . "',";
                } else {
                    $scopedFiltersText .= "'" . $scopeField . "'";
                }
            }

            $stringToReplace = 'return [];';
            TextHelper::replaceStringInFile($filename, $stringToReplace, $typeText);
        }

        // Move the file to Generated_files
        File::move($filename, storage_path('app/' . $generatedFilesPath . '/' . $typeName . '.php'));

        // Make folder in Generated_files and copy traits files into it
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Traits');
        File::copy(base_path('app/Traits/BaseModel.php'), storage_path('app/' . $generatedFilesPath . '/Traits/BaseModel.php'));
        File::copy(base_path('app/Traits/BootModel.php'), storage_path('app/' . $generatedFilesPath . '/Traits/BootModel.php'));
    }
}
