<?php namespace Muratsplat\Multilang;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

use Muratsplat\Multilang\Picker;
use Illuminate\Support\MessageBag;
use Muratsplat\Multilang\Validator;
use Muratsplat\Multilang\ValidatorWithNewRules as newRules;


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
	protected $defer = true;

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
            // adding new rules for our extention
            $this->addNewRules();

            $this->app->singleton('multilang', function($app) {               
            
                return new MultiLang(
                        
                        new Picker(new Collection(), new Element()),
                        $app['config'],
                        new MessageBag(),
                        new Validator($app['validator'], $app['config'])
                        );
            });
                
	}
        
        /**
         * to add new rules to Laravel Validator object
         */
        private function addNewRules() {
     
            $this->app['validator']->resolver(function($translator, $data, $rules, $messages) {
                
                return new newRules($translator, $data, $rules, $messages);
            });
        }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('multilang');
	}

}
