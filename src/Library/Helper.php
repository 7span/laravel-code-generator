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
     * @return array<string, string>
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
    public static function getTableNamesFromDB()
    {
        $tableNames = DB::table('information_schema.tables')
            ->select('TABLE_NAME as table_name')
            ->where('table_schema', config('database.connections.mysql.database'))
            ->where('TABLE_NAME', '!=', config('database.migrations.table', 'migrations')) // Exclude migrations table
            ->pluck('table_name')
            ->toArray();
        return $tableNames;
    }

    /**
     * Retrieves the names of all model files from the model directory.
     *
     * @return array
     */
    public static function getModelNames()
    {
        $modelPath = base_path(config('code-generator.paths.default.model'));
        if (!File::isDirectory($modelPath)) {
            return [];
        }

        $modelFiles = File::files($modelPath);
        $modelNames = [];

        foreach ($modelFiles as $file) {
            $modelNames[] = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }
        return $modelNames;
    }

    /**
     * Get column names for a given model name.
     *
     * @param string $modelName
     * @return array<int, string>
     */
    public static function getColumnsOfModel($modelName)
    {
        $fullModelClass = self::convertPathToNamespace(config('code-generator.paths.default.model')) . '\\' . $modelName;
        if (class_exists($fullModelClass)) {
            $model = new $fullModelClass();
            $tableName = method_exists($model, 'getTable') ? $model->getTable() : Str::plural(Str::snake(class_basename($modelName)));
            return Schema::hasTable($tableName) ? Schema::getColumnListing($tableName) : [];
        }
        return [];
    }

    /**
     * Get column names for a given table name.
     *
     * @param string $tableName
     * @return array<int, string>
     */
    public static function getColumnsOfTable($tableName)
    {
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

    public static function convertPathToNamespace(string $path): string
    {
        // Replace / with \ and trim slashes
        $segments = explode('/', trim($path, '/'));

        $studlySegments = array_map([Str::class, 'studly'], $segments);

        return implode('\\', $studlySegments);
    }
}
