<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Tests\Model\Content;
use Muratsplat\Multilang\Tests\Model\Image;
use Muratsplat\Multilang\CheckerAttribute;
// for testing CRUD ORM jobs..
use Muratsplat\Multilang\Tests\MigrateAndSeed;
use Muratsplat\Multilang\Tests\CreateContentAndLangTraitForTest;
use Mockery as m;

/**
 * a test class for \Muratsplat\Multilang\Wrapper
 *
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestCheckerAttribute extends MigrateAndSeed {    
   
    use CreateContentAndLangTraitForTest;
    /**
     * Main Model for test
     *
     * @var Muratsplat\Multilang\Tests\Model\Content 
     */
    private $content;
    
    /**
     * @var \Muratsplat\Multilang\CheckerAttribute;
     */
    private $checkerAttribute;  
    
    
        public function setUp() {
            parent::setUp();
           
            
            $config        =  $this->getMockedConfig();
            
            $config->shouldReceive('get')->with('multilang::cachePrefix')->andReturn('test');
            
            $config->shouldReceive('get')->with('multilang::rememberTime')->andReturn(1);
            
            $this->content = new Content();
            
            $this->image   = new Image();
            
            $this->checkerAttribute = new CheckerAttribute(
                    $this->app['db']->connection()->getSchemaBuilder(), 
                    $this->app['cache'],
                    $config
                    );            
        }        
        
        public function testSimpleFirst() {
                        
            //$this->app['db']->connection()->getSchemaBuilder()->getColumnListing('contents');
            
            $model = $this->content->create(['enable' => 1]);
            
            $this->assertFalse($this->checkerAttribute->check($model, 'name'));
            $this->assertTrue($this->checkerAttribute->check($model, 'id'));
            $this->assertfalse($this->checkerAttribute->check($model, 'notexist'));
            $this->assertTrue($this->checkerAttribute->check($model, 'visible'));
            $this->assertTrue($this->checkerAttribute->check($model, 'created_at'));            
        }
        
        public function tearDown() {
        
            parent::tearDown();        
            
            m::close();
        }
        
                  
        /**
         * 
         * @return \Mockery\MockInterface
         */
        protected function getMockedConfig() {
            
            return m::mock('Illuminate\Config\Repository');
                           
        }
        
}
