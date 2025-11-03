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

            $result['model_name'] = Str::studly(Str::singular($tableMatch[1][0]));
        }

        // Extract columns
        if (preg_match('/\((.*)\)/s', $sql, $matches)) {
            // Split by commas, ignoring commas inside parentheses
            $columnsRaw = preg_split('/,(?![^(]*\))/', $matches[1]);

            foreach ($columnsRaw as $col) {
                $col = trim($col);

                // Skip standalone FOREIGN KEY lines
                if (preg_match('/\b(FOREIGN|PRIMARY|UNIQUE)\s+KEY\b|\bCONSTRAINT\b|^\s*KEY\b/i', $col)) {
                    continue;
                }

                // Match column name + datatype
                if (preg_match('/`?(\w+)`?\s+(\w+)(\s*\(([^)]*)\))?/i', $col, $colMatch)) {
                    $columnName = $colMatch[1];
                    $dataType = self::mapSqlToLaravelDataType($colMatch[2]);

                    $field = [
                        'id' => Str::random(),
                        'column_name' => $columnName,
                        'data_type' => $dataType,
                        'is_fillable' => true,
                        'column_validation' => 'required',
                        'is_foreign_key' => false,
                        'is_nullable' => str_contains(strtolower($col), 'null') && !str_contains(strtolower($col), 'not null'),
                        'is_unique' => str_contains(strtolower($col), 'unique'),
                        'is_auto_increment' => str_contains(strtolower($col), 'auto_increment'),
                        'default_value' => null,
                    ];

                    // Handle DEFAULT values
                    if (preg_match('/DEFAULT\s+([^\s,]+)/i', $col, $defaultMatch)) {
                        $field['default_value'] = trim($defaultMatch[1], "'\"");
                    }

                    // Handle ENUM/SET values
                    if (in_array($dataType, ['enum', 'set']) && preg_match('/\((.*?)\)/', $col, $enumMatch)) {
                        $values = array_map(fn($v) => trim($v, " '\""), explode(',', $enumMatch[1]));
                        $field['enum_values'] = implode(',', $values);
                    }

                    // Extract inline foreign key if exists
                    if (preg_match('/REFERENCES\s+`?(\w+)`?\s*\(`?(\w+)`?\)/i', $col, $fkMatch)) {
                        $field['is_foreign_key'] = true;
                        $field['foreign_model_name'] = Str::singular(ucfirst($fkMatch[1]));
                        $field['referenced_column'] = $fkMatch[2];
                    }

                    $result['fields'][$columnName] = $field;
                }
            }

            //  Handle standalone FOREIGN KEY constraints
            preg_match_all(
                '/(?:CONSTRAINT\s+`?\w+`?\s+)?FOREIGN\s+KEY\s*\(`?(\w+)`?\)\s+REFERENCES\s+`?(\w+)`?\s*\(`?(\w+)`?\)(?:\s+ON\s+DELETE\s+(\w+(?:\s+\w+)?))?(?:\s+ON\s+UPDATE\s+(\w+(?:\s+\w+)?))?/i',
                $sql,
                $foreignMatches,
                PREG_SET_ORDER
            );

            foreach ($foreignMatches as $fk) {
                $column = $fk[1];
                $refTable = $fk[2];
                $refColumn = $fk[3];
                $onDelete = isset($fk[4]) ? trim($fk[4]) : null;
                $onUpdate = isset($fk[5]) ? trim($fk[5]) : null;

                if (isset($result['fields'][$column])) {
                    $result['fields'][$column]['is_foreign_key'] = true;
                    $result['fields'][$column]['foreign_model_name'] = Str::singular(ucfirst($refTable));
                    $result['fields'][$column]['referenced_column'] = $refColumn;
                    $result['fields'][$column]['on_delete_action'] = $onDelete ? strtolower($onDelete) : null;
                    $result['fields'][$column]['on_update_action'] = $onUpdate ? strtolower($onUpdate) : null;
                }
            }
        }

        $result['fields'] = array_values($result['fields']);
        return $result;
    }

    public static function convertPathToNamespace(string $path): string
    {
        $segments = explode('/', trim($path, '/'));

        $studlySegments = array_map([Str::class, 'studly'], $segments);

        return implode('\\', $studlySegments);
    }

    /**
     * Map SQL data types to Laravel migration method names.
     *
     * @param string 
     * @return string 
     */
    public static function mapSqlToLaravelDataType(string $sqlDataType): string
    {
        $sqlDataType = strtolower($sqlDataType);

        $mapping = [
            // Numeric types
            'tinyint' => 'tinyInteger',
            'smallint' => 'smallInteger',
            'mediumint' => 'mediumInteger',
            'int' => 'integer',
            'integer' => 'integer',
            'bigint' => 'bigInteger',
            'decimal' => 'decimal',
            'numeric' => 'decimal',
            'float' => 'float',
            'double' => 'double',
            'real' => 'double',
            'bit' => 'boolean',
            'boolean' => 'boolean',
            'bool' => 'boolean',
            'serial' => 'bigIncrements',

            // Date and time types
            'date' => 'date',
            'datetime' => 'dateTime',
            'timestamp' => 'timestamp',
            'time' => 'time',
            'year' => 'year',

            // String types
            'char' => 'char',
            'varchar' => 'string',
            'tinytext' => 'text',
            'text' => 'text',
            'mediumtext' => 'mediumText',
            'longtext' => 'longText',
            'binary' => 'binary',
            'varbinary' => 'binary',
            'tinyblob' => 'binary',
            'blob' => 'binary',
            'mediumblob' => 'binary',
            'longblob' => 'binary',
            'enum' => 'enum',
            'set' => 'set',

            // Spatial types
            'geometry' => 'geometry',
            'point' => 'point',
            'linestring' => 'lineString',
            'polygon' => 'polygon',
            'multipoint' => 'multiPoint',
            'multilinestring' => 'multiLineString',
            'multipolygon' => 'multiPolygon',
            'geometrycollection' => 'geometryCollection',

            // Other types
            'json' => 'json',
        ];

        return $mapping[$sqlDataType] ?? $sqlDataType;
    }
}
