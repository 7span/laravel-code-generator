<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;


class MakeLaravelDataCommand extends Command
{
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
        $fields = $request->get('table_fields') != null ? array_reverse($request->get('table_fields')) : [];
        $fieldsValidation = $this->makeLaravelDataValidation($fields);

        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile($fieldsValidation);

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
    public function getSourceFile($fieldsValidation)
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($fieldsValidation));
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


    public function makeValidationFormat($fieldName, $type, $validation, $charLimit)
    {
        // $strindDEmo= "#[Max(10)]
        // public string $name,

        // #[Max(10)]
        // public string $cname,

        // #[Required]
        // public ?string $password,";


        if($type == 'string') {
            $dataType = 'string';
        }else{
            $dataType='';
        }
        if($validation == 'required') {
            $validate = $validation;
            $validate = "#[Required]";
        }else{
            $validate='';
        }

        $string = ''.$validate."\n".'public '.$dataType.' $'.$fieldName;
        return $string;

        
        



    }

    public function makeLaravelDataValidation($fields)
    {
        $finalString='';
        
        foreach($fields as $key=>$value) {
            
            $jsonString = str_replace("'", '"', $value);
            $newArray = json_decode($jsonString, true);
            $fieldName = $key;
            $type= !empty($newArray['type']) ? $newArray['type'] : '';
            $validation = !empty($newArray['validation']) ? $newArray['validation'] : '';
            $charLimit = !empty($newArray['character_limit']) ? $newArray['character_limit'] : '';
            $makeValidateFormat = $this->makeValidationFormat($fieldName, $type, $validation, $charLimit);
            $finalString .=$makeValidateFormat;
            
        }
        return $finalString;
    }

    /**
     **
     * Map the stub variables present in stub to its value
     *
     * @return array
     */
    public function getStubVariables($fieldsValidation)
    {
        
        return [
            'NAMESPACE' => 'App\\Data\\' . $this->getSingularClassName($this->argument('name')),
            'CLASS_NAME' => $this->getSingularClassName($this->argument('name')) . 'Data',
            'SINGULAR_VARIABLE' => lcfirst($this->argument('name')),
            'FIELDS' => $fieldsValidation
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
