<?php

namespace App\Console\Commands;

use Str;
use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;

class MakeMutationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:mutation {name} {--fields=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make service file';

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
    {}
}
