<?php

namespace Sevenspan\CodeGenerator\Library;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class Helper
{
    /**
     * Get the relation label mapping.
     *
     * @return array<string, string> Array of relation 
     */
    public static function getRelationTypes(): array
    {
        return [
            'hasOne' => 'One to One',
            'hasMany' => 'One to Many',
            'belongsTo' => 'Belongs To',
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
     * @return array<int, string>
     */
    public static function getTableNamesFromMigrations()
    {
        // Get all migration records from the migrations table in database
        $allMigrationNames = DB::table(config('database.migrations.table'), 'migrations')->get();

        // Extract table names from migration file names
        $tableNames = collect($allMigrationNames)->map(function ($migrationRecord) {

            $migrationFileName = is_object($migrationRecord) ? $migrationRecord->migration : $migrationRecord['migration'];
            if (preg_match('/create_(.*?)_table/', $migrationFileName, $matches)) {
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

        // Try to resolve as a model class('App\Models\User'); fallback to table name if class does not exist
        if (class_exists($modelName)) {
            $model = new $modelName;
            $tableName = method_exists($model, 'getTable') && !empty($model->getTable())
                ? $model->getTable()
                : Str::plural(Str::snake(class_basename($modelName)));
        } else {
            // Assume it's a table name
            $tableName = Str::plural(Str::snake(class_basename($modelName)));
        }

        return Schema::hasTable($tableName)
            ? Schema::getColumnListing($tableName)
            : [];
    }
}
