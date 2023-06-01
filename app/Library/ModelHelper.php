<?php

namespace App\Library;

use File;
use Illuminate\Support\Str;
use App\Library\TextHelper;
use Illuminate\Support\Facades\Storage;

class ModelHelper
{
    const INDENT = '    ';

    public static function getModelName($string)
    {
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = Str::singular($string);
        $modelName = ucfirst($string);

        return $modelName;
    }

    public static function makeModel($modelName, $tableName, $fillableText, $generatedFilesPath, $scope, $softDelete,$deletedBy = '')
    {
        // Make model using command
        \Artisan::call('make:model ' . $modelName);

        $filename = base_path('app/Models/' . $modelName . '.php');

        
        // Replace the content table name of file as per our need
        $tableText = "table = '" . $tableName . "'";
        TextHelper::replaceStringInFile($filename, "table = ''", $tableText);

        $stringToReplace = '{{ softdelete }}';
        $replaceText = $softDelete == "1" ? "use Illuminate\Database\Eloquent\SoftDeletes;" : '';
        TextHelper::replaceStringInFile($filename, $stringToReplace, $replaceText);

        $stringToReplace = '{{ uses }}';
        $replaceText = "use BaseModel, BootModel, HasFactory".($softDelete == "1" ? ", SoftDeletes;" : ';');
        TextHelper::replaceStringInFile($filename, $stringToReplace, $replaceText);

        // Replace the content of file as per our need
        
        if(empty($deletedBy)){
            $fillableText .= PHP_EOL . self::INDENT . self::INDENT . "'deleted_by',";
        }
        $stringToReplace = 'fillable = [';
        $replaceWith = 'fillable = [' . $fillableText;
        TextHelper::replaceStringInFile($filename, $stringToReplace, $replaceWith);

        // Replace the content on based on soft delete checkbox
        $stringToReplace = "," . PHP_EOL . self::INDENT . self::INDENT . "'deleted_at' => 'datetime'";
        $replaceWith = $softDelete == "1" ? "," . PHP_EOL . self::INDENT . self::INDENT . "'deleted_at' => 'datetime'" : '';
        TextHelper::replaceStringInFile($filename, $stringToReplace, $replaceWith);

        $stringToReplace = ", 'deleted_at'];";
        $replaceWith = $softDelete == "1" ? ", 'deleted_at'];" : '];';
        TextHelper::replaceStringInFile($filename, $stringToReplace, $replaceWith);

        // Replace the content scopedFilters of file as per our need
        $scopedFiltersText = '';

        // Replace the content scopes of file as per our need
        $scopesText = '';
        if ($scope != '') {
            $scopeFields = explode(',', $scope);

            foreach ($scopeFields as $key => $scopeField) {
                $newScopeField = str_replace(' ', '', ucwords(str_replace('_', ' ', $scopeField)));
                if ($key == 0) {
                    $scopesText .= 'public function scope' . $newScopeField . '($' . 'query, ' . '$' . 'value)' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return  ' . '$' . "query->where('" . $scopeField . "', " . '$' . 'value);' . PHP_EOL . self::INDENT . '}' . PHP_EOL . PHP_EOL;
                } else {
                    $scopesText .= self::INDENT . 'public function scope' . $newScopeField . '($' . 'query, ' . '$' . 'value)' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return  ' . '$' . "query->where('" . $scopeField . "', " . '$' . 'value);' . PHP_EOL . self::INDENT . '}' . PHP_EOL . PHP_EOL;
                }

                if (array_key_last($scopeFields) != $key) {
                    $scopedFiltersText .= "'" . $scopeField . "',";
                } else {
                    $scopedFiltersText .= "'" . $scopeField . "'";
                }
            }

            $stringToReplace = 'scopedFilters = [';
            $replaceWith = $stringToReplace . $scopedFiltersText;
            TextHelper::replaceStringInFile($filename, $stringToReplace, $replaceWith);

            $stringToReplace = '{{ scopes }}';
            TextHelper::replaceStringInFile($filename, $stringToReplace, $scopesText);
        } else {
            $stringToReplace = '{{ scopes }}';
            TextHelper::replaceStringInFile($filename, $stringToReplace, $scopesText);
        }

        // Move the file to Generated_files
        File::move($filename, storage_path('app/' . $generatedFilesPath . '/' . $modelName . '.php'));
    }
}
