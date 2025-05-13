<?php

namespace Sevenspan\CodeGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codegenerator:resource {modelName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a resource class for the specified model';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //TODO :to be implemented , working on this 
    }
}
