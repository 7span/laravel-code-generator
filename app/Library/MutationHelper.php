<?php

namespace App\Library;

use File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MutationHelper
{
    const INDENT = '    ';

    public static function getMutationName($string)
    {
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = Str::singular($string);
        $mutationName = ucfirst($string);

        return $mutationName;
    }

    public static function makeMutation($mutationName, $fields)
    {
        // Make model using command
        \Artisan::call('make:mutation ' . $mutationName . '--fields=ok');

        $filename = base_path('app/GraphQL/Mutation/' . $mutationName . '.php');

        dd($fields);

        // Replace the content table name of file as per our need
        $mutationText = '';
        if ($scope != '') {
            $scopeFields = explode(',', $scope);

            foreach ($scopeFields as $key => $scopeField) {
                $newScopeField = str_replace(' ', '', ucwords(str_replace('_', ' ', $scopeField)));
                if ($key == 0) {
                    $mutationText .= 'public function scope' . $newScopeField . '($' . 'query, ' . '$' . 'value)' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return  ' . '$' . "query->where('" . $scopeField . "', " . '$' . 'value)' . PHP_EOL . self::INDENT . '}' . PHP_EOL . PHP_EOL;
                } else {
                    $mutationText .= self::INDENT . 'public function scope' . $newScopeField . '($' . 'query, ' . '$' . 'value)' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return  ' . '$' . "query->where('" . $scopeField . "', " . '$' . 'value)' . PHP_EOL . self::INDENT . '}' . PHP_EOL . PHP_EOL;
                }

                if (array_key_last($scopeFields) != $key) {
                    $scopedFiltersText .= "'" . $scopeField . "',";
                } else {
                    $scopedFiltersText .= "'" . $scopeField . "'";
                }
            }

            $stringToReplace = 'return [];';
            TextHelper::replaceStringInFile($filename, $stringToReplace, $mutationText);
        }

        // Move the file to Generated_files
        File::move($filename, storage_path('app/' . $generatedFilesPath . '/' . $mutationName . '.php'));

        // Make folder in Generated_files and copy traits files into it
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Traits');
        File::copy(base_path('app/Traits/BaseModel.php'), storage_path('app/' . $generatedFilesPath . '/Traits/BaseModel.php'));
        File::copy(base_path('app/Traits/BootModel.php'), storage_path('app/' . $generatedFilesPath . '/Traits/BootModel.php'));
    }
}
