<?php

namespace App\Library;

use File;
use App\Library\TextHelper;

class MigrationHelper
{
    public static function makeMigration($tableName, $migrationText, $generatedFilesPath)
    {
        // Make migration file using command
        \Artisan::call('make:migration create_' . $tableName . '_table');

        // Replace the content of file as per our need
        $files = scandir(base_path('database/migrations'), SCANDIR_SORT_DESCENDING);
        $newest_file = $files[0];
        $filename = base_path('database/migrations/' . $newest_file);
        $stringToReplace = 'table->id();';
        $replaceWith = 'table->id();' . $migrationText;
        TextHelper::replaceStringInFile($filename, $stringToReplace, str_replace('"', "'", $replaceWith));

        // Move migration file to Generated_files
        File::move(base_path('database/migrations/' . $newest_file), storage_path('app/' . $generatedFilesPath . '/' . $newest_file));
    }
}
