<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Tests\Model\Content;
use Muratsplat\Multilang\Tests\Model\Image;
use Muratsplat\Multilang\Wrapper;
// for testing CRUD ORM jobs..
use Muratsplat\Multilang\Tests\MigrateAndSeed;
use \Mockery as m;

/**
 * a test class for \Muratsplat\Multilang\Wrapper
 *
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestWrapper  extends MigrateAndSeed {
    
    
    /**
     * Main Model for test
     *
     * @var Muratsplat\Multilang\Tests\Model\Content 
     */
    private $content;
    
    /**
     * @var Muratsplat\Multilang\Tests\Model\Image
     */
    private $image;
    
    /**
     * Object which will be tested!
     *  
     * @var Muratsplat\Multilang\Wrapper 
     */
    private $wrapper;
    
    /**
     *
     * @var array 
     */
    private $items;
    
    
        public function setUp() {
            parent::setUp();
            
            $mockedConfig = m::mock('Illuminate\Config\Repository')->shouldReceive('get')
                    ->with('multilang::reservedAttribute')
                    ->andReturn('__lang_id__')->getMock();          
           
            $this->wrapper = new Wrapper($mockedConfig);
            
            $this->content = new Content();
            
            $this->image   = new Image();
            
        }
       
        public function createContent($n) {
            
            for ($i=0; $i< $n ; $i++) {
                
                Content::create(['enable' => '1', 'visible' => 2])->save();                
          
            }          
            return $this->content->all()->count() === $n;    
        }
        
        public function createContentLang($n) {
            
            $callback = function($item) use ($n) {
              
                for ($i=0; $i< $n ; $i++) {
                
                    $item->ContentLangs()->create([
                    
                    '__lang_id__' => $i, 
                    'title' => str_random(5),
                    'content' => str_random(10)
                    ])->save();                
                }               
            };
            
            Content::all()->each($callback);
            
            return Content::find(1)->ContentLangs()->getResults()->count() === $n;
        }      
        
        public function testSimpleFirst() {
            
            $this->assertTrue($this->createContent(1));
            
            $this->assertTrue($this->createContentLang(6));
            
            $content = Content::find(1);
                                    
            $model = $this->wrapper->createNew($content,1, 1);
            
            $this->assertTrue($model->isExistedOnMain('visible'));            
            
            $this->assertFalse($model->isExistedOnMain('olmayan'));
            
            $this->assertTrue($model->isExistedOnLangModel('title'));
            
            $this->assertFalse($model->isExistedOnLangModel('olmayan'));
            
            $this->assertEquals(5, strlen($model->title));
            
            // for __isset methods
            
            $this->assertTrue(isset($model->visible));            
            
            $this->assertFalse(isset($model->olmayan));
            
            $this->assertTrue(isset($model->title));
            
            $this->assertFalse(isset($model->olmayan));            
        }
        
        
        public function createImages() {
            
            $content = new Content(['enable' => 1, 'visible' => 1]);
            
            $content->save();
            
            for($i = 0;$i < 5; $i++) {
            
                 $content->Images()->create(['file_name' => str_random()])->save();               
            }
            
            return $content->Images;
        }
        
        public function testWarapperWithRelations() {
            
            $this->assertCount(5, $this->createImages());
            
            $content = $this->content->all()->last();            
            
            $this->assertInstanceOf('Muratsplat\Multilang\Tests\Model\Content', $content);
            
            $wrapper = $this->wrapper->createNew($content);
            
            $this->assertEquals(1, $wrapper->enable);
            
            $this->assertEquals(1, $wrapper->visible);
            
            $this->assertEquals($content->Images, $wrapper->Images);
            
            $this->assertCount(5, $content->Images);
            
            $this->assertCount(5, $wrapper->Images);           
        }
        
        public function testWarapperWithRelationsIssueUndefinedProperty() {
            
            $content = new Content(['enable' => 1, 'visible' => 1]);
            
            $content->save();
                        
            $wrapper = $this->wrapper->createNew($content,1,1);
           
            $this->assertCount(0, $wrapper->Images);    
        }
        
        public function testOverLoadingMethod() {
            
            $this->assertTrue($this->createContent(1));
            
            $this->assertTrue($this->createContentLang(6));
            
            $content = Content::find(1);
                                    
            $wrapper = $this->wrapper->createNew($content,1, 1);
            
            $this->assertEquals("Hi, I'am method on this model!", $wrapper->someMethod());                        
        }       
}
