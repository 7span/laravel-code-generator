<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

class MakeLaravelDataCommand extends Command
{

    const INDENT = '     ';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:laravel-data {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a laraveldata file';


    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }


    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Request $request)
    {
        // Get table name
        $tableName = strtolower(Str::plural(preg_replace('/\B([A-Z])/', '_$1', $request->model_name)));

        $fields = $request->get('table_fields') != null ? array_reverse($request->get('table_fields')) : [];

        $fieldsValidation = $this->makeLaravelDataValidation($fields, $tableName);
        $laravelClassAdded = $this->makeLaravelDataClassAdd($fields);


        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile($fieldsValidation, $laravelClassAdded);

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }


    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     */
    public function getSourceFile($fieldsValidation, $laravelClassAdded)
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($fieldsValidation, $laravelClassAdded));
    }

    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param  array  $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents($stub, $stubVariables = [])
    {

        $contents = file_get_contents($stub);


        foreach ($stubVariables as $search => $replace) {

            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }


    public function laravelDataClassAdded()
    {
    }

    public function makeValidationFormat($fieldName, $type, $validation, $charLimit,  $tableName)
    {

        $checkDataType = ['enum', 'foreignKey', 'uuid'];


        if (!in_array($type, $checkDataType)) {

            $dataTypeWithoutString = [
                'integer',
                'bigInteger',
                'mediumInteger',
                'tinyInteger',
                'smallInteger',
                'unsignedBigInteger',
                'unsignedInteger',
                'unsignedMediumInteger',
                'unsignedSmallInteger',
                'unsignedTinyInteger',
                'boolean',
                'enum',
                'uuid',
                'date',
                'foreignKey'
            ];

            // public ?DateTime $created_at
            $validate = '';

            $optionalSymbol = '';
            $charLimit = !empty($charLimit) ? $charLimit : '';

            if (!in_array($type, $dataTypeWithoutString)) {
                $dataType = 'string';
            } else {
                if ($type == 'date') {
                    $dataType = 'DateTime';
                } elseif ($type == 'boolean') {
                    $dataType = 'bool';
                } else {
                    $dataType = 'int';
                    //  $charLimit = !empty($charLimit) ? '11' : $charLimit;
                }
            }

            if ($validation == 'optional') {
                $optionalSymbol = '?';
            }

            if ($validation == 'required') {
                if (!empty($charLimit)) {
                    $validate = "#[Required, Max($charLimit)]";
                } elseif ($type == 'decimal') {
                    $validate = "#[Required, Numeric]";
                } elseif ($type == 'float') {
                    $validate = "#[Required, Numeric]";
                } elseif ($type == 'double') {
                    $validate = "#[Required, Numeric]";
                } elseif ($type == 'boolean') {
                    $validate = "#[Required, BooleanType]";
                } elseif ($type == 'date') {
                    $validate = "#[Required, DateFormat('Y-m-d')]";
                } else {
                    $validate = "#[Required]";
                }

                $variableAdd = 'public ' . $dataType . ' $' . $fieldName . ',' . "\n";
            } elseif ($validation == 'optional') {
                if (!empty($charLimit)) {
                    $validate = "#[Max($charLimit)]";
                } elseif ($type == 'date') {
                    $validate = "#[DateFormat('Y-m-d')]";
                } elseif ($type == 'boolean') {
                    $validate = "#[BooleanType]";
                } elseif ($type == 'decimal') {
                    $validate = "#[Numeric]";
                } elseif ($type == 'float') {
                    $validate = "#[Numeric]";
                } elseif ($type == 'double') {
                    $validate = "#[Numeric]";
                }
                $variableAdd = 'public ' . $optionalSymbol . $dataType . ' $' . $fieldName . ',' . "\n";
            } elseif ($validation == 'unique') {
                if (!empty($charLimit)) {

                    $validate = '#[Unique(' . "'$tableName'" . ', ' . "'$fieldName'" . '), Max(' . $charLimit . ')]';
                } elseif ($type == 'decimal') {
                    $validate = '#[Numeric, Unique(' . "'$tableName'" . ', ' . "'$fieldName'" . ')]';
                } else {
                    $validate = '#[Unique(' . "'$tableName'" . ', ' . "'$fieldName'" . ')]';
                }

                $variableAdd = 'public ' . $dataType . ' $' . $fieldName . ',' . "\n";
            } elseif ($validation == 'email') {
                if (!empty($charLimit)) {
                    $validate = '#[Email,Unique(' . "'$tableName'" . ', ' . "'$fieldName'" . '), Max(' . $charLimit . ')]';
                } else {
                    $validate = '#[Email,Unique(' . "'$tableName'" . ', ' . "'$fieldName'" . ')]';
                }

                $variableAdd = 'public ' . $dataType . ' $' . $fieldName . ',' . "\n";
            } else {
                if ($type == 'decimal') {
                    $validate = "#[Numeric]";
                    $variableAdd = 'public ' . $dataType . ' $' . $fieldName . ',' . "\n";
                } elseif ($type == 'float') {
                    $validate = "#[Numeric]";
                    $variableAdd = 'public ' . $dataType . ' $' . $fieldName . ',' . "\n";
                } elseif ($type == 'double') {
                    $validate = "#[Numeric]";
                    $variableAdd = 'public ' . $dataType . ' $' . $fieldName . ',' . "\n";
                } elseif ($type == 'date') {
                    $validate = "#[DateFormat('Y-m-d')]";
                    $variableAdd = 'public ' . $dataType . ' $' . $fieldName . ',' . "\n";
                } else {

                    $validate = "#[Max($charLimit)]";
                    $validate = !empty($charLimit) ? $validate : '';
                    $variableAdd = 'public ' . $optionalSymbol . $dataType . ' $' . $fieldName . ',' . "\n";
                }
            }




            $string = "\n" . '' . self::INDENT . $validate . "\n" . self::INDENT . $variableAdd;

            return $string;
        }

        return '';
    }

    // Added Dynamically Validation laravel data

    public function makeLaravelDataValidation($fields, $tableName)
    {
        $finalString = '';

        foreach ($fields as $key => $value) {

            $jsonString = str_replace("'", '"', $value);
            $newArray = json_decode($jsonString, true);
            $fieldName = $key;
            $type = !empty($newArray['type']) ? $newArray['type'] : '';
            $validation = !empty($newArray['validation']) ? $newArray['validation'] : '';
            $charLimit = !empty($newArray['character_limit']) ? $newArray['character_limit'] : '';

            $makeValidateFormat = $this->makeValidationFormat($fieldName, $type, $validation, $charLimit, $tableName,);
            if (!empty($makeValidateFormat)) {
                $finalString .= $makeValidateFormat;
            }
        }


        return $finalString;
    }

    public function makeLaravelDataClassAdd($fields)
    {

        $checkDataType = ['enum', 'foreignKey', 'uuid'];



        $dataTypes = [
            'integer',
            'bigInteger',
            'mediumInteger',
            'tinyInteger',
            'smallInteger',
            'unsignedBigInteger',
            'unsignedInteger',
            'unsignedMediumInteger',
            'unsignedSmallInteger',
            'unsignedTinyInteger',
            'foreignKey'
        ];


        $classAddArray = [
            'required' => [
                'count' => 0,
                'laravelDataClass' => 'use Spatie\LaravelData\Attributes\Validation\Required;'
            ],
            'max' => [
                'count' => 0,
                'laravelDataClass' => 'use Spatie\LaravelData\Attributes\Validation\Max;'
            ],
            'unique' => [
                'count' => 0,
                'laravelDataClass' => 'use Spatie\LaravelData\Attributes\Validation\Unique;'
            ],
            'email' => [
                'count' => 0,
                'laravelDataClass' => 'use Spatie\LaravelData\Attributes\Validation\Email;'
            ],
            'date' => [
                'count' => 0,
                'laravelDataClass' => 'use DateTime;'
            ],
            'date_format' => [
                'count' => 0,
                'laravelDataClass' => 'use Spatie\LaravelData\Attributes\Validation\DateFormat;'
            ],
            'numeric' => [
                'count' => 0,
                'laravelDataClass' => 'use Spatie\LaravelData\Attributes\Validation\Numeric;'
            ],
            'boolean' => [
                'count' => 0,
                'laravelDataClass' => 'use Spatie\LaravelData\Attributes\Validation\BooleanType;'
            ]
        ];
        
           
        foreach ($fields as  $value) {

            $jsonString = str_replace("'", '"', $value);

            $newArray = json_decode($jsonString, true);
         
            $validation = !empty($newArray['validation']) ? $newArray['validation'] : '';

           
                    
            $charLimit = !empty($newArray['character_limit']) ? $newArray['character_limit'] : '';
           
            $type = !empty($newArray['type']) ? $newArray['type'] : '';


            if (!in_array($type, $checkDataType)) {
             
                if (in_array($type, $dataTypes)) {
                    $charLimit = '11';
                } else {
                    if ($type == 'date') {
                        $charLimit = '';
                    }
                }
            
                 
                if ($validation == 'required') {
                    
                    $classAddArray['required']['count'] = 1;

                    // Log::info('laravelRequired  ' .$key);
                }
                if (!empty($validation) && $validation == 'email') {
                    $classAddArray['email']['count'] = 1;
                }
                if (!empty($charLimit)) {
                    $classAddArray['max']['count'] = 1;
                    Log::info('laravelMax');
                }
                if ($validation == 'unique') {
                    $classAddArray['unique']['count'] = 1;
                    Log::info('laravelunique');
                }
                if ($type == 'date') {
                    $classAddArray['date']['count'] = 1;
                    $classAddArray['date_format']['count'] = 1;
                } elseif ($type == 'decimal') {
                    $classAddArray['numeric']['count'] = 1;
                } elseif ($type == 'boolean') {
                    $classAddArray['boolean']['count'] = 1;
                } elseif ($type == 'float') {
                    $classAddArray['numeric']['count'] = 1;
                } elseif ($type == 'double') {
                    $classAddArray['numeric']['count'] = 1;
                }
            }
          
            $laravelDataFacadeClass = $this->addlaravelDataFacade($classAddArray);

           
        }
        if(!empty($laravelDataFacadeClass)){
            return $laravelDataFacadeClass;
        }else{
            return '';
        }
       
    }



    public function addlaravelDataFacade($laravelDataFacade)
    {
        $finalStringFacade = '';
        foreach ($laravelDataFacade as $value) {
            if ($value['count'] > 0) {
                $finalStringFacade .=   $value['laravelDataClass'] . "\n";
            }
        }

        return $finalStringFacade;
    }

    /**
     **
     * Map the stub variables present in stub to its value
     *
     * @return array
     */
    public function getStubVariables($fieldsValidation, $laravelDataFacadeAdded)
    {

        return [
            'NAMESPACE' => 'App\\Data\\' . $this->getSingularClassName($this->argument('name')),
            'CLASS_NAME' => $this->getSingularClassName($this->argument('name')) . 'Data',
            'SINGULAR_VARIABLE' => lcfirst($this->argument('name')),
            'FIELDS' => $fieldsValidation,
            'FACADE' => $laravelDataFacadeAdded
        ];
    }


    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/laravel-data.stub';
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {

        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
        return $path;
    }

    public function getSourceFilePath()
    {
        //return base_path('app/Http/Data') . '/' . $this->getSingularClassName($this->argument('name')) . 'LaravelData.php';
        $sigularClassName = $this->getSingularClassName($this->argument('name'));
        // return base_path('app/Http/Data') . '/' . $sigularClassName . '/'.$sigularClassName.'Data.php';
        return base_path('app/Data') . '/' . $sigularClassName . '/' . $sigularClassName . 'Data.php';
    }

    /**
     * Return the Singular Capitalize Name
     *
     * @return string
     */
    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }
}
