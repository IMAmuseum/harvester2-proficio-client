<?php

namespace Imamuseum\ProficioClient;

use Illuminate\Support\ServiceProvider;

class ProficioServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (config('proficio.routes_enabled')) {
            include __DIR__.'/Http/routes.php';
        }

        $this->publishes([
            __DIR__.'/../config/proficio.php' => config_path('proficio.php'),
        ]);

        $this->mergeConfigFrom(__DIR__.'/../config/proficio.php', 'proficio');
    }
}
