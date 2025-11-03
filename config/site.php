<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SQL ↔ Laravel Migration Data Type Mappings
    |--------------------------------------------------------------------------
    | This mapping helps convert between SQL column types and Laravel migration
    | methods in both directions. Common and frequently used types are grouped
    | for better organization.
    |--------------------------------------------------------------------------
    */

    'data_types' => [

        // 🔹 SQL → Laravel (for parsing SQL schema)
        'parse_migration' => [
            // 🔹 String Types
            'varchar'        => 'string',
            'char'           => 'char',
            'text'           => 'text',
            'mediumtext'     => 'mediumText',
            'longtext'       => 'longText',
            'enum'           => 'enum',
            'set'            => 'set',

            // 🔹 Numeric Types
            'tinyint'        => 'tinyInteger',
            'smallint'       => 'smallInteger',
            'mediumint'      => 'mediumInteger',
            'int'            => 'integer',
            'bigint'         => 'bigInteger',
            'decimal'        => 'decimal',
            'float'          => 'float',
            'double'         => 'double',
            'boolean'        => 'boolean',

            // 🔹 Date & Time
            'date'           => 'date',
            'datetime'       => 'dateTime',
            'timestamp'      => 'timestamp',
            'time'           => 'time',
            'year'           => 'year',

            // 🔹 Binary / JSON
            'binary'         => 'binary',
            'varbinary'      => 'binary',
            'blob'           => 'binary',
            'tinyblob'       => 'binary',
            'mediumblob'     => 'binary',
            'longblob'       => 'binary',
            'json'           => 'json',

            // 🔹 Spatial
            'geometry'             => 'geometry',
            'point'                => 'point',
            'linestring'           => 'lineString',
            'polygon'              => 'polygon',
            'multipoint'           => 'multiPoint',
            'multilinestring'      => 'multiLineString',
            'multipolygon'         => 'multiPolygon',
            'geometrycollection'   => 'geometryCollection',
        ],
         // Grouped options for UI (lists Laravel types per group)
        'groups' => [
            'Common fields' => [
                'integer' => 'int',
                'string'  => 'varchar',
                'text'    => 'text',
                'date'    => 'date',
            ],

            'Numeric' => [
                'tinyInteger'      => 'tinyint',
                'smallInteger'     => 'smallint',
                'mediumInteger'    => 'mediumint',
                'integer'          => 'int',
                'bigInteger'       => 'bigint',
                'decimal'          => 'decimal',
                'float'            => 'float',
                'double'           => 'double',
                'boolean'          => 'boolean',
            ],

            'Date and time' => [
                'date'      => 'date',
                'dateTime'  => 'datetime',
                'timestamp' => 'timestamp',
                'time'      => 'time',
                'year'      => 'year',
            ],

            'String' => [
                'char'       => 'char',
                'string'     => 'varchar',
                'text'       => 'text',
                'mediumText' => 'mediumtext',
                'longText'   => 'longtext',
                'enum'       => 'enum',
                'set'        => 'set',
                'binary'     => 'blob',
            ],

            'Spatial' => [
                'geometry'           => 'geometry',
                'point'              => 'point',
                'lineString'         => 'linestring',
                'polygon'            => 'polygon',
                'multiPoint'         => 'multipoint',
                'multiLineString'    => 'multilinestring',
                'multiPolygon'       => 'multipolygon',
                'geometryCollection' => 'geometrycollection',
            ],

            'Other' => [
                'json' => 'json',
            ],
        ],
    ],
];
