<?php

namespace Webvelopers\CRUDGenerator;

use Illuminate\Support\ServiceProvider;
use Webvelopers\CRUDGenerator\Console\CRUDGeneratorCommand;

class CRUDGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CRUDGeneratorCommand::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}