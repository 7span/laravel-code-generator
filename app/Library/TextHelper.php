<?php

namespace App\Library;

class TextHelper
{
    const INDENT = '    ';

    // A function to make replaceable text
    public static function getReplaceableText($tableFields, $tableName)
    {
        // Make replaceable text for model fillable, migration field text and request rule text
        $migrationText = '';
        $ruleText = '';
        $fillableText = '';

        if ($tableFields != null) {
            foreach ($tableFields as $field => $values) {
                $val = get_object_vars(json_decode(str_replace("'", '"', $values)));
                $fieldType = $val['type'];

                $validation = explode('|', $val['validation']);
                if (in_array('optional', $validation)) {
                    unset($validation[array_search('optional', $validation)]);
                }

                if (!in_array('required', $validation)) {
                    array_push($validation, 'nullable');
                }

                $possibleValues = $val['possible_values'];
                $field = str_replace(' ', '_', $field);
                // $null_or_not_null = $validation != 'required' ? '->nullable()' : '';
                $null_or_not_null = !in_array('required', $validation) ? '->nullable()' : '';

                if ($fieldType == 'enum') {
                    $pVal = '';
                    $length = count(explode(',', $possibleValues));

                    foreach (explode(',', $possibleValues) as $key => $v) {
                        if ($key == 0) {
                            $first = $v;
                        }
                        $pVal .= $length == $key + 1 ? "'" . $v . "'" : "'" . $v . "',";
                    }
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->' . $fieldType . '("' . $field . '" , [' . $pVal . '])->default("' . $first . '");';
                } elseif ($fieldType == 'decimal' || $fieldType == 'double' || $fieldType == 'float') {
                    $val = get_object_vars(json_decode(str_replace("'", '"', $values)));
                    $totalNumber = $val['total_number'];
                    $decimalPrecision = $val['decimal_precision'];
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->' . $fieldType . '("' . $field . '", ' . $totalNumber . ', ' . $decimalPrecision . ');';
                } elseif ($fieldType == 'tinyInteger') {
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->' . $fieldType . '("' . $field . '")->default("0");';
                } elseif ($fieldType == 'string') {
                    $val = get_object_vars(json_decode(str_replace("'", '"', $values)));
                    $characterLimit = $val['character_limit'];
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->string("' . $field . '", ' . $characterLimit . ')' . $null_or_not_null . ';';
                } elseif ($fieldType == 'foreignKey') {
                    $val = get_object_vars(json_decode(str_replace("'", '"', $values)));
                    $foreignKeyTableName = $val['table_name'];
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->foreign("' . $field . '")->references("id")->on("' . $foreignKeyTableName . '")->onDelete("CASCADE");' . PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->index("' . $field . '");';
                } else {
                    $migrationText .= PHP_EOL . self::INDENT . self::INDENT . self::INDENT . '$table->' . $fieldType . '("' . $field . '")' . $null_or_not_null . ';';
                }

                if (in_array('required', $validation) && array_key_last($tableFields) != $field) {
                    $ruleText .= '"' . $field . '" => "' . implode('|', $validation) . '",' . PHP_EOL;
                } elseif (in_array('required', $validation) && array_key_last($tableFields) == $field) {
                    $ruleText .= self::INDENT . '"' . $field . '" => "' . implode('|', $validation) . '"';
                } else {
                    $ruleText .= '"' . $field . '" => "' . implode('|', $validation) . '",' . PHP_EOL;
                }

                $ruleText = str_replace('unique', 'unique:' . $tableName, $ruleText);

                $fillableText .= PHP_EOL . self::INDENT . self::INDENT . "'" . $field . "',";
            }
        }

        return [$migrationText, $ruleText, $fillableText];
    }

    public static function replaceStringInFile($filename, $stringToReplace, $replaceWith)
    {
        $content = file_get_contents($filename);
        $contentChunks = explode($stringToReplace, $content);
        $content = implode($replaceWith, $contentChunks);
        file_put_contents($filename, $content);
    }
}
