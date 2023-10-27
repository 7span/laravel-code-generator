<?php

namespace App\Library;

// use File;
use Illuminate\Support\Str;
use App\Library\TextHelper;
use Illuminate\Support\Facades\File;
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

    public static function removeSpecialChar($string)
    {
        $string = str_replace(' ', '_', $string);
        $string = str_replace('-', '_', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        return $string;
    }

    public static function makeModel($modelName, $tableName, $fillableText, $generatedFilesPath, $scope, $softDelete, $deletedBy = '', $trait = '', $relationArr = '')
    {
        $relationModel =  isset($relationArr['relationModel']) ? $relationArr['relationModel'] : [];
        $relationShip =  isset($relationArr['relationShip']) ? $relationArr['relationShip'] : [];
        $relationAnotherModel =  isset($relationArr['relationAnotherModel']) ? $relationArr['relationAnotherModel'] : [];
        $foreignKeyArr =  isset($relationArr['foreignKey']) ? $relationArr['foreignKey'] : [];
        $localKey =  isset($relationArr['localKey']) ? $relationArr['localKey'] : [];

        // Make model using command
        \Artisan::call('make:model ' . 'Models/' . $modelName);

        $filename = base_path('app/Models/' . $modelName . '.php');
        // Replace the content table name of file as per our need
        $tableText = "table = '" . $tableName . "'";
        TextHelper::replaceStringInFile($filename, "table = ''", $tableText);

        $stringToReplace = '{{ softdelete }}';
        $replaceText = $softDelete == "1" ? "use Illuminate\Database\Eloquent\SoftDeletes;" : '';
        TextHelper::replaceStringInFile($filename, $stringToReplace, $replaceText);

        $stringToReplace = '{{ uses }}';
        $replaceText = "use BaseModel, BootModel, HasFactory" . ($softDelete == "1" ? ", SoftDeletes;" : ';');
        TextHelper::replaceStringInFile($filename, $stringToReplace, $replaceText);

        // Replace the content of file as per our need

        if (empty($deletedBy)) {
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
                    $scopesText .= 'public function scope' . $newScopeField . '($' . 'query, ' . '$' . 'value)' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return  ' . '$' . "query->where('" . $scopeField . "', " . '$' . 'value);' . PHP_EOL . self::INDENT . '}' . PHP_EOL;
                } else {
                    $scopesText .= self::INDENT . 'public function scope' . $newScopeField . '($' . 'query, ' . '$' . 'value)' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return  ' . '$' . "query->where('" . $scopeField . "', " . '$' . 'value);' . PHP_EOL . self::INDENT . '}' . PHP_EOL;
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
        $bootTrait = '';
        if (empty($trait)) {
            $bootTrait = 'public static function boot()' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'parent::boot();' . PHP_EOL . self::INDENT . self::INDENT . 'static::creating(function ($model) { ' . PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$model->created_by = auth()->id(); ' . PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$model->updated_by = auth()->id();' . PHP_EOL . self::INDENT . self::INDENT . '});'  . PHP_EOL . self::INDENT . '}' . PHP_EOL;
        }

        $stringToReplace = '{{ bootTrait }}';
        TextHelper::replaceStringInFile($filename, $stringToReplace, $bootTrait);

        $mainModel = lcfirst($modelName);
        $relationData = '';

        if (!empty($relationModel)) {
            foreach ($relationModel as $rkey => $val) {
                if (!empty($val)) {
                    $relationShipVal = isset($relationShip[$rkey]) ? $relationShip[$rkey] : '';
                    $relationShipSecondModel = isset($relationAnotherModel[$rkey]) ? $relationAnotherModel[$rkey] : '';
                    $localKey = isset($localKey[$rkey]) ? $localKey[$rkey] : '';

                    $foreignKey = isset($foreignKeyArr[$rkey]) ? $foreignKeyArr[$rkey] : '';

                    $modelname = ModelHelper::getModelName(ucfirst($val));
                    $secondModel = $modelRelationName =  ModelHelper::getModelName(lcfirst($val));

                    if (in_array($relationShipVal, ['hasMany', 'belongsToMany', 'hasManyThrough', 'morphMany', 'morphToMany'])) {
                        $modelRelationName = Str::plural($modelRelationName);
                    }

                    $secondArg = '';
                    if ($relationShipVal == 'belongsToMany') {
                        $secondArg = ", '" . lcfirst($secondModel) . '_' . $mainModel . "'";
                    }
                    if ($relationShipVal == 'hasMany' || $relationShipVal == 'belongsToMany') {

                        if (!empty($foreignKey))
                            $secondArg .= ", '" . $foreignKey . "'";
                        if (!empty($localKey))
                            $secondArg .= ", '" . $localKey . "'";
                    }


                    if ($relationShipVal == 'hasOneThrough' || $relationShipVal == 'hasManyThrough') {

                        if (empty($relationShipSecondModel)) {
                            continue;
                        }

                        $secondModel = lcfirst($relationShipSecondModel);

                        if ($relationShipVal == 'hasOneThrough') {
                            $modelRelationName .= ucfirst($secondModel);
                        }

                        if ($relationShipVal == 'hasManyThrough') {
                            $modelRelationName = Str::plural(lcfirst($secondModel));
                        }

                        $modelname = ucfirst($secondModel);
                        $secondArg = ucfirst($val);

                        if (!empty($foreignKey)) {
                            $mainModelId = $tableName = strtolower(preg_replace('/\B([A-Z])/', '_$1', $modelName)) . "_id";
                            $secondArg = ", " . ucfirst($val) . "::class, '" . $mainModelId . "', '" . $foreignKey . "'";
                        } else {
                            $secondArg = ", " . ucfirst($val) . "::class";
                        }
                    }
                    if ($relationShipVal == 'morphMany' || $relationShipVal == 'morphToMany') {
                        $secondArg = ", '" . lcfirst($val) . "able'";
                    }
                    if ($relationShipVal == 'morphOne') {
                        $secondArg = ", '" . lcfirst($val) . "able'";
                    }

                    $newintend = '';
                    if ($rkey != 0) {
                        $newintend = self::INDENT;
                    }
                    if (empty($secondArg)) {
                        if (!empty($foreignKey)) {
                            $secondArg .= ", '" . $foreignKey . "'";
                        }

                        if (!empty($localKey)) {
                            $secondArg .= ", '" . $localKey . "'";
                        }
                    }
                    $relationData .= $newintend . 'public function ' . lcfirst($modelRelationName) . '()' . PHP_EOL . self::INDENT . '{' . PHP_EOL . self::INDENT . self::INDENT . 'return $this->' . $relationShipVal . '(' . $modelname . '::class' . $secondArg . ');' . PHP_EOL . self::INDENT . '}' . PHP_EOL . "\r\n";
                }
            }
        }

        $stringToReplace = '{{ relation }}';
        TextHelper::replaceStringInFile($filename, $stringToReplace, $relationData);

        Storage::disk('local')->makeDirectory($generatedFilesPath . '/Models');
        File::move($filename, storage_path('app/' . $generatedFilesPath . '/Models/' . $modelName . '.php'));

        // Delete the Models folder
        File::deleteDirectory(base_path('app/Models'));
    }
}
