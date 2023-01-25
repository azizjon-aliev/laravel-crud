<?php

namespace Azizjonaliev\Laravelcrud\Console\Commands;

use Azizjonaliev\Laravelcrud\CRUD\CrudGenerate;
use Illuminate\Console\Command;

class LaravelCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud
        {name : The name of the class}
        {--Version= : Separates files into version folders}
        {--S|service : Creates a Service file and for your controller}
        {--R|request : Create new StoreRequest and UpdateRequest files for the controller}
        {--RR|resource : Create a new file Resource for the controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generates files for CRUD operation depending on the selected option and frees you from the routine of work";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CrudGenerate $generator)
    {
        $generator->api->name = $this->argument('name');
        $generator->api->request = $this->option('request');
        $generator->api->service = $this->option('service');
        $generator->api->version = $this->option('Version');
        $generator->api->resource = $this->option('resource');
        $this->printInfoMessage($generator->api->generate());
    }

    private function printInfoMessage($arr)
    {
        foreach ($arr as $key => $value) {

            if (is_array($value))
                $this->printInfoMessage($value);

            elseif ($value)
                $this->components->info("{$key} [{$value}] created successfully.");

            elseif ($value !== null && $value === false)
                $this->components->error("{$key} already exists.");
        }
    }
}
