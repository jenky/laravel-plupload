<?php namespace Jenky\LaravelPlupload;

use Illuminate\Support\ServiceProvider;

class PluploadServiceProvider extends ServiceProvider {

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
		$configPath = __DIR__ . '/../config/plupload.php';

		$this->mergeConfigFrom($configPath, 'plupload');

		$this->app['plupload'] = $this->app->share(function($app)
		{
			return $app->make('Jenky\LaravelPlupload\Plupload', [
				'request' => $app['request']
			]);
		});
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$configPath = __DIR__ . '/../config/plupload.php';
		$viewsPath = __DIR__.'/../views';
		$assetsPath = __DIR__.'/../assets';
		
		$this->loadViewsFrom($viewsPath, 'plupload');

		$this->publishes([$configPath => config_path('plupload.php')], 'config');
		$this->publishes([
			$viewsPath => base_path('resources/views/vendor/plupload'),
			$assetsPath . '/js' => base_path('resources/assets/plupload'),
		]);
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
