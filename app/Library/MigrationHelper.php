<?php

namespace App\Library;

use File;
use Illuminate\Support\Facades\Storage;

class MigrationHelper
{
    const INDENT = '    ';

    public static function makeMigration($tableName, $migrationText, $generatedFilesPath, $softDelete, $deletedBy = '')
    {
        // Make migration file using command
        \Artisan::call('make:migration create_' . $tableName . '_table');

        // Replace the content of file as per our need
        $files = scandir(base_path('database/migrations'), SCANDIR_SORT_DESCENDING);
        $newest_file = $files[0];
        $filename = base_path('database/migrations/' . $newest_file);

        $stringToReplace = "table->smallIncrements('id')->index();";
        $replaceWith = "table->smallIncrements('id')->index();" . $migrationText;
        TextHelper::replaceStringInFile($filename, $stringToReplace, str_replace('"', "'", $replaceWith));

        $stringToReplace = '$table->timestamps();' . PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->softDeletes();';
        $replaceWith = $softDelete == '1' ? '$table->timestamps();' . PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->softDeletes();' : '$table->timestamps();';
        if (empty($deletedBy)) {
            $deleted_by = 'deleted_by';
            $replaceWith .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$' . "table->integer('" . $deleted_by . "')->nullable();";
        }
        TextHelper::replaceStringInFile($filename, $stringToReplace, $replaceWith);

        // Move migration file to Generated_files
        Storage::disk('local')->makeDirectory($generatedFilesPath . '/database/migrations');
        File::move(base_path('database/migrations/' . $newest_file), storage_path('app/' . $generatedFilesPath . '/database/migrations/' . $newest_file));
    }
}
