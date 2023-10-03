<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;


class MakeNotificatonCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:notification';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a notification file';


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
       
          $notificationData =[
                'class_name' => $request->class_name,
                'subject' => $request->subject,
                'data' => $request->data,
                'body' => $request->body
            ];

          
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));
        
        $contents = $this->getSourceFile($notificationData);

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
    public function getSourceFile($notificationData)
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($notificationData));
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


    /**
     **
     * Map the stub variables present in stub to its value
     *
     * @return array
     */
    public function getStubVariables($notificationData)
    {

        return [    
            
            'CLASS_NAME' => $notificationData['class_name'],
            'SUBJECT' => $notificationData['subject'],
            'BODY' => $notificationData['body'],
            'DATA' => $notificationData['data'],

        ];
    }


    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/notification.stub';
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
        
        $sigularClassName = $this->getSingularClassName($_REQUEST['class_name']);
        return base_path('app/Notifications') . '/' . $sigularClassName . '.php';
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
