<?php

namespace Azizjonaliev\Laravelcrud\Providers;

use Azizjonaliev\Laravelcrud\Console\Commands\LaravelCrudCommand;
use Illuminate\Support\ServiceProvider;

class LaravelCrudProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                LaravelCrudCommand::class
            ]);
        }
    }
}
