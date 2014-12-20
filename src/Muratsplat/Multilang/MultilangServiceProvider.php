<?php namespace Muratsplat\Multilang;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection as collecter;
use Muratsplat\Multilang\Picker;

/* MultiLang Service Provider
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
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
