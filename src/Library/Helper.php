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
    public static function getRelationTypes(): array
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
    public static function getTableNamesFromMigrations()
    {
        $migrationPath = base_path(config('code-generator.paths.migration', 'Database\Migrations'));
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

    /**
     * Parse a CREATE TABLE SQL statement and extract model name and fields.
     *
     * @param string $sql The SQL statement to parse
     * @return array<string, mixed> Parsed model name and fields
     */
    public static function parseCreateTable(string $sql): array
    {
        $result = [
            'model_name' => '',
            'fields' => [],
        ];

        // Extract model name
        if (preg_match('/CREATE\s+TABLE\s+`?(\w+)`?/i', $sql, $tableMatch)) {
            $result['model_name'] = Str::singular(ucfirst($tableMatch[1]));
        }

        // Extract columns
        if (preg_match('/\((.*)\)/s', $sql, $matches)) {
            $columnsRaw = explode(',', $matches[1]);

            foreach ($columnsRaw as $col) {
                if (preg_match('/`?(\w+)`?\s+(\w+)/', trim($col), $colMatch)) {
                    $result['fields'][] = [
                        'id' => Str::random(),
                        'column_name' => Str::snake($colMatch[1]),
                        'data_type' => Str::lower($colMatch[2]),
                        'column_validation' => 'required',
                    ];
                }
            }
        }

        return $result;
    }
}
