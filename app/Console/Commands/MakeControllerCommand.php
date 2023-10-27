<?php

namespace App\Console\Commands;

use App\Library\TextHelper;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;

class MakeControllerCommand extends Command
{
    const INDENT = '    ';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:controller API/V1/{name}Controller {--methods=} {--service=} {--resource=} {--requestFile=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make api controller file';

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile();

        if (! $this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }

    /**
     * Return the stub file path
     *
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/controller.stub';
    }

    /**
     **
     * Map the stub variables present in stub to its value
     *
     * @return array
     */
    public function getStubVariables()
    {
        return [
            'NAMESPACE' => 'App\\Http\\Controllers\\Api\\V1',
            'CLASS_NAME' => $this->getSingularClassName($this->argument('name')),
            'SINGULAR_VARIABLE' => lcfirst($this->argument('name')),
            'PLURAL_VARIABLE' => Str::plural(strtolower($this->argument('name'))),
            'MODEL_NAME' => ucfirst($this->argument('name')),
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     */
    public function getSourceFile()
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }

    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param  array  $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents($stub, $stubVariables = [])
    {
        $main_stub = __DIR__ . '/../../../stubs/controller.stub';

        $upperContents = file_get_contents($main_stub);

        foreach ($stubVariables as $search => $replace) {
            $upperContents = str_replace('$' . $search . '$', $replace, $upperContents);
        }

        $methods = explode(',', $this->option('methods'));
        $service = $this->option('service');
        $resource = $this->option('resource');
        $requestFile = $this->option('requestFile');

        $stringToReplace = '{{ service }}';
        $replaceText = "".($service == "1" ? 'use App\Services' . '\\' . $stubVariables['CLASS_NAME'] . 'Service;' : '');
        $upperContents = str_replace($stringToReplace, $replaceText, $upperContents);

        $stringToReplace = '{{ request }}';
        $replaceText = "".($requestFile == "1" ? 'use App\Http\Requests' . '\\' . $stubVariables['CLASS_NAME'] . '\\' . 'Request as '.$stubVariables['CLASS_NAME'].'Request;' : '');
        $upperContents = str_replace($stringToReplace, $replaceText, $upperContents);

        $stringToReplace = '{{ resource }}';
        $replaceText = "".($requestFile == "1" ? 'use App\Http\Resources' . '\\' . $stubVariables['CLASS_NAME'] . '\\' . 'Resource as '.$stubVariables['CLASS_NAME'].'Resource;' : '');
        $upperContents = str_replace($stringToReplace, $replaceText, $upperContents);

        $stringToReplace = '{{ singularService }}';
        $replaceText = "".($service == "1" ? 'private $' . $stubVariables['SINGULAR_VARIABLE'] . 'Service;' : '');
        $upperContents = str_replace($stringToReplace, $replaceText, $upperContents);

        $stringToReplace = '{{ serviceObj }}';
        $replaceText = "".($service == "1" ? '$this->' . $stubVariables['SINGULAR_VARIABLE'] . 'Service = new '.$stubVariables['CLASS_NAME'].'Service;' : '');
        $upperContents = str_replace($stringToReplace, $replaceText, $upperContents);

        $methodContents = '';

        foreach ($methods as $method) {
            $className = $stubVariables['CLASS_NAME'];
            if ($method == 'show') {
                $string_to_replace = 'use App\Http\Controllers\Controller;';

                $replace_with = $string_to_replace . PHP_EOL . 'use App\Http\Resources' . '\\' . $className . '\Resource as ' . $className . 'Resource;';
                $replace_with = ($resource == '1') ? $replace_with : $string_to_replace;
                $upperContents = str_replace($string_to_replace, $replace_with, $upperContents);
            } elseif ($method == 'index') {
                $string_to_replace = 'use App\Http\Controllers\Controller;';
                $replace_with = $string_to_replace . PHP_EOL . 'use App\Http\Resources' . '\\' . $className . '\Collection as ' . $className . 'Collection;';
                $replace_with = ($resource == '1') ? $replace_with : $string_to_replace;
                $upperContents = str_replace($string_to_replace, $replace_with, $upperContents);
            }

            $stub = __DIR__ . '/../../../stubs/controller.' . $method . '.stub';

            $stubVariables = $this->getStubVariables();
            $contents = file_get_contents($stub);

            $singularVariable = '$'.$stubVariables['SINGULAR_VARIABLE'];

            $stringToReplace = '{{ requestName }}';
            $replaceText = "".($requestFile == "1" ? $className.'Request $request' : '');
            $contents = str_replace($stringToReplace, $replaceText, $contents);

            $stringToReplace = '{{ updaterRequestName }}';
            $replaceText = "".($requestFile == "1" ? $className.' '.$singularVariable.', '.$className.'Request $request' : $className.' '.$singularVariable);
            $contents = str_replace($stringToReplace, $replaceText, $contents);

            $stringToReplace = '{{ indexMethod }}';
            $resourceExist = ($resource == '1') ? 'return $this->collection(new '.$className.'Collection($'.Str::plural($stubVariables['SINGULAR_VARIABLE']).'));' : 'return $this->success($'.Str::plural($stubVariables['SINGULAR_VARIABLE']).");";
            $replaceText = "".($service == "1" ? '$'.Str::plural($stubVariables['SINGULAR_VARIABLE']).' = $this->'.$stubVariables['SINGULAR_VARIABLE'].'Service->collection($request->all());'.PHP_EOL . self::INDENT . self::INDENT .$resourceExist : '');
            $contents = str_replace($stringToReplace, $replaceText, $contents);


            $stringToReplace = '{{ storeMethod }}';
            $singluarObj = $singularVariable.'Obj';
            $error = $singularVariable.'Obj["errors"]';
            $ifRequest = ($requestFile == "1" ? '$request->all()' : '');
            $replaceText = "".($service == "1" ? $singluarObj .' = $this->'.$stubVariables['SINGULAR_VARIABLE'].'Service->store('.$ifRequest.');'.PHP_EOL . self::INDENT . self::INDENT .'return isset('.$error.') ? $this->error('.$singluarObj.') : $this->success('.$singluarObj.');' : '');
            $contents = str_replace($stringToReplace, $replaceText, $contents);


            $stringToReplace = '{{ showMethod }}';
            $id = $singularVariable.'->id';
            $resourceViewExist = ($resource == '1') ? 'return $this->resource(new '.$className.'Resource('.$singluarObj.'));' : 'return $this->success('.$singularVariable.'Obj);';
            $replaceText = "".($service == "1" ? $singularVariable.'Obj = $this->'.$stubVariables['SINGULAR_VARIABLE'].'Service->resource('.$id.');'.PHP_EOL . self::INDENT . self::INDENT .$resourceViewExist : '');
            $contents = str_replace($stringToReplace, $replaceText, $contents);

            $stringToReplace = '{{ updateMethod }}';

            $ifUpdateRequest = ($requestFile == "1" ? ', $request->all()' : '');
            $replaceText = "".($service == "1" ? $singluarObj.' = $this->'.$stubVariables['SINGULAR_VARIABLE'].'Service->update('.$id.''.$ifUpdateRequest.');'.PHP_EOL . self::INDENT . self::INDENT .'return isset('.$error.') ? $this->error('.$singluarObj.') : $this->success('.$singluarObj.');' : '');
            $contents = str_replace($stringToReplace, $replaceText, $contents);

            $stringToReplace = '{{ destroyMethod }}';
            $replaceText = "".($service == "1" ? $singularVariable.' = $this->'.$stubVariables['SINGULAR_VARIABLE'].'Service->destroy('.$id.');'.PHP_EOL . self::INDENT . self::INDENT .'return $this->success('.$singularVariable.');' : '');
            $contents = str_replace($stringToReplace, $replaceText, $contents);

            foreach ($stubVariables as $search => $replace) {
                $contents = str_replace('$' . $search . '$', $replace, $contents);
            }

            $methodContents .= PHP_EOL . $contents;
        }

        $fullContents = $upperContents . $methodContents . '}' . PHP_EOL;

        return $fullContents;
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath()
    {

        return base_path('app/Http/Controllers/API/V1') . '/' . $this->getSingularClassName($this->argument('name')) . 'Controller.php';
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

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    public function replace_string_in_file($filename, $string_to_replace, $replace_with)
    {
        $content = file_get_contents($filename);
        $content_chunks = explode($string_to_replace, $content);
        $content = implode($replace_with, $content_chunks);
        file_put_contents($filename, $content);
    }
}
