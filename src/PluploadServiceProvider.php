<?php

namespace Jenky\LaravelPlupload;

use Illuminate\Support\ServiceProvider;

class PluploadServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPlupload();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $configPath = __DIR__.'/../config/plupload.php';
        $viewsPath = __DIR__.'/../resources/views';
        $assetsPath = __DIR__.'/../resources/assets';
        $translationsPath = __DIR__.'/../resources/lang';

        $this->mergeConfigFrom($configPath, 'plupload');
        $this->loadViewsFrom($viewsPath, 'plupload');
        $this->loadTranslationsFrom($translationsPath, 'plupload');

        $this->publishes([$configPath => config_path('plupload.php')], 'config');
        $this->publishes([
            $viewsPath        => base_path('resources/views/vendor/plupload'),
            $assetsPath.'/js' => base_path('resources/assets/plupload'),
            $translationsPath => base_path('resources/lang/vendor/plupload'),
        ]);
    }

    /**
     * Register the plupload class.
     *
     * @return void
     */
    protected function registerPlupload()
    {
        $this->app->singleton('plupload', function ($app) {
            $request = $app['request'];
            $config = $app['config'];

            return new Plupload($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['plupload'];
    }
}
