<?php namespace TagPlanet\UniversalAnalytics;

use Illuminate\Support\ServiceProvider;

class UniversalAnalyticsServiceProvider extends ServiceProvider
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
		$this->package('tag-planet/universal-analytics');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerUniversalAnalytics();
	}

    /**
     * Register the application bindings.
     *
     * @return void
     */
    protected function registerUniversalAnalytics()
    {
        $this->app->bind('universal-analytics', function($app)
        {
            return new UniversalAnalytics($app);
        });
    }

}