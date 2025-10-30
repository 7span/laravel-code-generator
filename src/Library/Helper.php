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
        if (preg_match_all('/CREATE\s+TABLE\s+`?(\w+)`?/i', $sql, $tableMatch)) {
            if (count($tableMatch[1]) > 1) {
                return [
                    'error' => 'Multiple table names detected: ' . implode(', ', $tableMatch[1])
                ];
            }

            $result['model_name'] = Str::singular(ucfirst($tableMatch[1][0]));
        }

        // Extract columns
        if (preg_match('/\((.*)\)/s', $sql, $matches)) {
            // Split by commas, ignoring commas inside parentheses
            $columnsRaw = preg_split('/,(?![^(]*\))/', $matches[1]);

            foreach ($columnsRaw as $col) {
                $col = trim($col);

                // Match column name and data type
                if (preg_match('/`?(\w+)`?\s+(\w+)(\s*\(([^)]*)\))?/i', $col, $colMatch)) {
                    $columnName = $colMatch[1];
                    $dataType = strtolower($colMatch[2]);

                    $field = [
                        'id' => Str::random(),
                        'column_name' => Str::lower($columnName),
                        'data_type' => $dataType,
                        'is_fillable' => true,
                        'column_validation' => 'required',
                    ];

                    // Extract ENUM or SET values
                    if (in_array($dataType, ['enum', 'set']) && preg_match('/\((.*?)\)/', $col, $enumMatch)) {
                        $values = array_map(fn($v) => trim($v, " '\""), explode(',', $enumMatch[1]));
                        $field['enum_values'] = implode(',', $values);
                    }

                    // Extract inline foreign key if exists
                    if (preg_match('/REFERENCES\s+`?(\w+)`?\s*\(`?(\w+)`?\)/i', $col, $fkMatch)) {
                        $field['is_foreign_key'] = true;
                        $field['foreign_model_name'] = $fkMatch[1];
                        $field['referenced_column'] = $fkMatch[2];

                        // Match ON UPDATE and ON DELETE actions
                        if (preg_match('/ON\s+UPDATE\s+([A-Z\s]+)/i', $col, $onUpdateMatch)) {
                            $field['on_update_action'] = strtolower(trim($onUpdateMatch[1]));
                        }
                        if (preg_match('/ON\s+DELETE\s+([A-Z\s]+)/i', $col, $onDeleteMatch)) {
                            $field['on_delete_action'] = strtolower(trim($onDeleteMatch[1]));
                        }
                    }

                    $result['fields'][] = $field;
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
