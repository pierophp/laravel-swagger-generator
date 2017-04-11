<?php

namespace LaravelSwaggerGenerator\Console\Commands;

use Illuminate\Console\Command;
use LaravelSwaggerGenerator\Core\Generator;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger-generator:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate initial docs to Swagger';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $generator = new Generator();
        $generator->generate();
    }

}
