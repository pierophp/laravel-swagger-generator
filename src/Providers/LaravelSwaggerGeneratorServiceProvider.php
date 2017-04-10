<?php

namespace LaravelSwaggerGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelSwaggerGenerator\Console\Commands\GenerateCommand;

class LaravelSwaggerGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->commands('swagger-generator.generate');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommand();
    }

    /**
     * Register the Artisan command.
     */
    protected function registerCommand()
    {
        $this->app->singleton('swagger-generator.generate', function () {
            return new GenerateCommand();
        });
    }
}