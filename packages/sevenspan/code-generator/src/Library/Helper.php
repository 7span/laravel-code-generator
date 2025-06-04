<?php

namespace Sevenspan\CodeGenerator\Library;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class Helper
{
    /**
     * Get the relation label mapping.
     *
     * @return array<string, string> Array of relation 
     */
    public static function getRelation(): array
    {
        return [
            'hasOne' => 'One to One',
            'hasMany' => 'One to Many',
            'belongsToMany' => 'Many to Many',
            'hasOneThrough' => 'Has One Through',
            'hasManyThrough' => 'Has Many Through',
            'morphOne' => 'One To One (Polymorphic)',
            'morphMany' => 'One To Many (Polymorphic)',
            'morphToMany' => 'Many To Many (Polymorphic)',
        ];
    }

    /**
     * Load migration table names from the migrations directory.
     *
     * @return array<int, string> List of table names found in migration files
     */
    public static function loadMigrationTableNames()
    {
        $migrationPath = database_path('migrations');
        $files = File::exists($migrationPath) ? File::files($migrationPath) : [];

        $tableNames = collect($files)->map(function ($file) {
            if (preg_match('/create_(.*?)_table/', $file->getFilename(), $matches)) {
                return $matches[1];
            }
            return null;
        })->filter()->unique()->values()->toArray();
        return $tableNames;
    }

    /**
     * Get column names for a given model's table.
     *
     * @param string $modelName
     * @return array<int, string> List of column names
     */
    public static function getColumnNames($modelName)
    {
        $tableName = Str::plural(Str::snake($modelName));
        if (Schema::hasTable($tableName)) {
            $columnNames = Schema::getColumnListing($tableName);
        } else {
            $columnNames = [];
        }

        return $columnNames;
    }
}
