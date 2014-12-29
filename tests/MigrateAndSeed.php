<?php namespace Muratsplat\Multilang\Tests;


// for testing CRUD ORM jobs..
use Orchestra\Testbench\TestCase;

/**
 * Base Class make to prepare database environment
 * for ORM jobs in need.
 *
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class MigrateAndSeed extends TestCase {
    
        /**
         * When each and every test method works, first it will run
         */
        public function setUp() {
            parent::setUp();
            
            // Create an artisan object for calling migrations
            $artisan = $this->app->make('artisan');
             // Call migrations specific to our tests, e.g. to seed the db
            $artisan->call('migrate', array(
                    '--database' => 'testbench',
                    '--path' => '../tests/database/migrate',
            ));
            
            //By default, the db:seed command runs the DatabaseSeeder class, 
            //which may be used to call other seed classes. However, 
            //you may use the --class option to specify a specific 
            //seeder class to run individually:
            //php artisan db:seed --class=UserTableSeeder
            
            $artisan->call('db:seed', array('--class' => 'DatabaseSeeder'));        
        }
        

        /**
        * Define environment setup.
        *
        * @param Illuminate\Foundation\Application $app
        * @return void
        */
        protected function getEnvironmentSetUp($app)
        {   ///home/muratsplat/projects/multilang/workbench/muratsplat/multilang/tests
            // reset base path to point to our package's src directory
            $app['path.base'] = __DIR__ . '/../src';
            // set up database configuration
            $app['config']->set('database.default', 'testbench');
            $app['config']->set('database.connections.testbench', array(
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ));

        }        
        
        /**
        * Get Multilang package providers.
        *
        * @return array
        */
        protected function getPackageProviders() {
            
               // return array('Muratsplat\Multilang\SluggableServiceProvider');
            
            return array();
        }
        
        public function testExample() {
            
        }
    
}