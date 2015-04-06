<?php namespace Muratsplat\Multilang;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Validator;
use Muratsplat\Multilang\NewRule;
use Muratsplat\Multilang\Wrapper;
use Muratsplat\Multilang\CheckerAttribute;

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
	public function boot()	{
            
            $this->package('muratsplat/multilang');

            // adding new rules for our extention
            $this->addNewRules();
            
            // setting laravel events object
            MultiLang::setEventDispatcher($this->app['events']);
        }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
            $this->app->singleton('multilang', function($app) {               
            
                return new MultiLang(
                            new Picker(new Collection(), new Element(),$app['config']),
                            $app['config'],
                            new MessageBag(),
                            new Validator($app['validator'], $app['config']),
                            new Wrapper(
                                    $app['config'], 
                                    new CheckerAttribute(
                                            $app['db']->connection()->getSchemaBuilder(), 
                                            $app['cache'], 
                                            $app['config']
                                            )
                                    ),
                            $app['events']                           
                        );
            });
          
	}
            
        /**
         * to add new rules to Laravel Validator object
         */
        private function addNewRules() {
  
            $this->app['validator']->extend('RequiredForDefaultLang', 'Muratsplat\Multilang\NewRule@validateRequiredForDefaultLang');
            
            $newRule = new NewRule();
            
            $this->app['validator']->replacer('RequiredForDefaultLang', $newRule->replaceRequiredForDefaultLang());            
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
