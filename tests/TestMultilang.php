<?php namespace Muratsplat\Multilang\Tests;

use Illuminate\Support\Collection;
use Illuminate\Config\Repository as Config;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Tests\Base;
use Muratsplat\Multilang\Element;
use Muratsplat\Multilang\MultiLang;
use Muratsplat\Multilang\Validator;
use Muratsplat\Multilang\Tests\Model\Content;
//use Muratsplat\Multilang\Tests\Migrate\Contents as migrateContent;

// for testing CRUD ORM jobs..
use Orchestra\Testbench\TestCase;
use \Mockery as m;
use Illuminate\Validation\Validator as laravelValidator;


/**
 * a test class for \Muratsplat\Multilang\MultiLang
 *
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestMultilang extends TestCase {    
    
    /**
     *
     * @var Muratsplat\Multilang\MultiLang
     */
    private $multiLang;
    
        public function tearDown() {
        
            parent::tearDown();        
            
            m::close();
        }
        
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
        
        /**
         * 
         * @return \Mockery\MockInterface
         */
        protected function getMockedConfig() {
            
            return m::mock('Illuminate\Config\Repository','Illuminate\Config\LoaderInterface');            
        }
        
        /**
         * 
         * @return \Mockery\MockInterface
         */
        protected function getMockedMessageBag() {
            
            return m::mock('Illuminate\Support\MessageBag');
        }
        
        /**
         * 
         * @return \Mockery\MockInterface
         */
        protected function getMockedValid() {
            
            return m::mock('Muratsplat\Multilang\Validator');
        }

        public function testCheckMainImplement() {
            
            $mockedConfig = $this->getMockedConfig();            
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $validator->shouldReceive('make')->andReturn(true);
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element()),
                    new Content(), 
                    $mockedConfig, 
                    $messageBag,
                    $validator);

            $this->assertTrue($multiLang->create(['visible'=>1], new Content()));
    
        }
        
        public function testCheckContentsMigration() {
            
            $content = new Content(['enable'=>1, 'visible' => 1]);
            
            $this->assertTrue($content->save());
            
            $this->assertEquals(1, $content->enable);
            
            $this->assertEquals(1, $content->visible);            
            
        }
        
        public function testCheckContentLangRelation() {
            
            $content = new Content(['enable'=>1, 'visible' => 1]);
            
            $content->save();
            
            $this->assertInstanceOf('Muratsplat\Multilang\Tests\Model\ContentLang', $content->ContentLangs()->getRelated());           
        }
        
        public function testTryCRUDContentLang() {        
            
            $content = new Content(['enable'=>1, 'visible' => 1]);
            
            $content->save();
            
            $records = [
                
                ['__lang_id__' => 1, 'title' => 'Foo', 'content' => 'Baar'],
                ['__lang_id__' => 2, 'title' => 'FooBus', 'content' => 'Bobuus'],
            ];
            
            $createdRecords = $content->ContentLangs()->createMany($records);
            
            $this->assertTrue($content->save());
           $this->assertEquals(2, count($createdRecords));
        }
        
        public function testWithNonMultilangPost() {
            
            $mockedConfig = $this->getMockedConfig();            
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
              
            $validator->shouldReceive('make')->andReturn(true);
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element()),
                    new Content(), 
                    $mockedConfig, 
                    $messageBag,
                    $validator);

            $post = ['enable' => 1, 'visible' => 1 ];
            $this->assertTrue($multiLang->create($post, new Content()));
            
            $this->assertEquals(1, Content::all()->count());
            
            $this->assertEquals($post['enable'], Content::find(1)->enable);
            $this->assertEquals($post['visible'], Content::find(1)->visible);
        }
        
        public function testWithMultilangPost() {
            
            $mockedConfig = $this->getMockedConfig();            
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);            
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element()),
                    new Content(), 
                    $mockedConfig, 
                    $messageBag,
                    $validator);

            $post = [
                    'enable' => 1, 
                    'visible' => 1, 
                    'content@1' => 'test',
                    'title@1' => 'Title test',
                    
                    'content@2' => 'test',
                    'title@2' => 'Title test',
   
                    ];
            $this->assertTrue($multiLang->create($post, new Content()));            
            
            $this->assertEquals(1, Content::all()->count());            
            
            $this->assertEquals(2, count(Content::find(1)->ContentLangs));           
        }
        
        public function testCreateEmptyPostData() {
            
            $mockedConfig = $this->getMockedConfig();            
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);            
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element()),
                    new Content(), 
                    $mockedConfig, 
                    $messageBag,
                    $validator);

            $post = [];
            try {
                
                $multiLang->create($post, new Content());
                
                $this->assertTrue(false);
                
            } catch (\Muratsplat\Multilang\Exceptions\MultilangPostEmpty $ex) {
                
                return $this->assertTrue(true);

            }
              
                
            
        }
        
        public function testSimpleUpdate() {
            
            $mockedConfig = $this->getMockedConfig();            
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element()),
                    new Content(), 
                    $mockedConfig, 
                    $messageBag,
                    $validator);
             
             
             $post = [
                    'enable' => 1, 
                    'visible' => 1, 
                    'content@1' => 'test',
                    'title@1' => 'Title test',
                    
                    'content@2' => 'test',
                    'title@2' => 'Title test',
   
                    ];
             
            $created = new Content();
            
            $created->visible = 1;
            
            $created->save();
             
            $this->assertTrue($multiLang->update($post, $created));         
            
        }
}
