<?php namespace Muratsplat\Multilang;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection as collecter;
use Muratsplat\Multilang\Picker;

class MultilangServiceProvider extends ServiceProvider {

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
		$this->package('muratsplat/multilang');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

            $this->app->bind('pickerML', function() {
           
                return new Picker(new collecter());
            });
                
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
