<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Tests\Model\Content;
use Muratsplat\Multilang\Tests\Model\ContentSoftDelete;
use Muratsplat\Multilang\Tests\Model\Image;
use Muratsplat\Multilang\Wrapper;
use Muratsplat\Multilang\CheckerAttribute;
// for testing CRUD ORM jobs..
use Muratsplat\Multilang\Tests\MigrateAndSeed;
use \Mockery as m;
use Muratsplat\Multilang\Tests\CreateContentAndLangTraitForTest;

/**
 * a test class for \Muratsplat\Multilang\Wrapper
 *
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestWrapper extends MigrateAndSeed {    
   
    use CreateContentAndLangTraitForTest;
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
            
            $configForChecker = $this->getMockedConfig();
            
            $configForChecker->shouldReceive('get')->with('multilang::cachePrefix')->andReturn('/test/multilang');
            
            $configForChecker->shouldReceive('get')->with('multilang::rememberTime')->andReturn(1);
            
            $mockedConfig = m::mock('Illuminate\Config\Repository')->shouldReceive('get')
                    ->with('multilang::reservedAttribute')
                    ->andReturn('__lang_id__')->getMock();                       
            $this->wrapper = new Wrapper(
                    $mockedConfig, 
                    new CheckerAttribute(
                            $this->app['db']->connection()->getSchemaBuilder(), 
                            $this->app['cache'],
                            $configForChecker)
                    );
            
            $this->content = new Content();
            
            $this->image   = new Image();
            
        }
        
        
        public function tearDown() {
        
            parent::tearDown();        
            
            m::close();
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
            
            $this->assertEquals("Hi, I'am method on main model!", $wrapper->someMethod());                        
        }
        
        public function testIssueOfGettingWrongLangModel() {
            
            $this->assertTrue($this->createContent(3));
            
            $contentFirst = Content::find(1);
            
            $postFirst = ['__lang_id__' => 1, 'title' => 'First Title', 'content' => 'First Content'];
            
            $contentFirst->ContentLangs()->create($postFirst);
            
            $contentLast = Content::find(3);
            
            $postLast = ['__lang_id__' => 1, 'title' => 'Last Title', 'content' => 'Last Content'];
           
            $contentLast->ContentLangs()->create($postLast);
            
            //var_dump($contentLast->ContentLangs()->getQuery()->where('__lang_id__', 1)->get()->count());
            
            $wrapperFirst = $this->wrapper->createNew($contentFirst);
            
            $this->assertEquals($postFirst['title'], $wrapperFirst->title);
            
            $wrapperLast = $this->wrapper->createNew($contentLast);
            
            $this->assertEquals($postLast['title'], $wrapperLast->title);
        }        
    
        
        public function testWantedMethodOnRuntime() {
            
            $this->assertTrue($this->createContent(3));
                      
            /* first */
            $content = Content::find(1);
            $postFirst = ['__lang_id__' => 1, 'title' => 'First Title', 'content' => 'First Content'];
            $content->ContentLangs()->create($postFirst);
            
            /* second */
            $postSecond = ['__lang_id__' => 2, 'title' => 'Second Title', 'content' => 'Second Content'];            
            $content->ContentLangs()->create($postSecond);
            
            /* third */            
            $postThird = ['__lang_id__' => 3, 'title' => 'Third Title', 'content' => 'Third Content'];
            $content->ContentLangs()->create($postThird);
            
            $wrapper = $this->wrapper->createNew($content,1);            
            
            $this->assertEquals($wrapper->title, $postFirst['title']);           
          
            $this->assertEquals($wrapper->wanted(2)->title, $postSecond['title']);
           
            $this->assertEquals($wrapper->wanted(3)->title, $postThird['title']);            
        }
        
        public function testFoundedBugOnNullAttributes() {
            
            $content = new Content(['enable' => 1, 'visible' => 1,  'alias' => null]);
            
            $post = ['__lang_id__' => 1, 'title' => null, 'content' => null];
            
            $this->assertTrue($content->save());
            
            $content->ContentLangs()->create($post);
            
            $wrapper = $this->wrapper->createNew($content);
            
            $this->assertEquals(null, $wrapper->title);
            
            $this->assertEquals(null, $wrapper->title);
            
            $this->assertEquals(null, $wrapper->alias);            
        } 
        
        public function testOnSoftDeleteEnebledModels() {
            
            $this->assertTrue($this->createContentSoftDelete(3));
                      
            /* first */
            $content = ContentSoftDelete::find(1);
            $postFirst = ['__lang_id__' => 1, 'title' => 'First Title', 'content' => 'First Content'];
            $content->ContentLangs()->create($postFirst);
            
            /* second */
            $postSecond = ['__lang_id__' => 2, 'title' => 'Second Title', 'content' => 'Second Content'];            
            $content->ContentLangs()->create($postSecond);
            
            /* third */            
            $postThird = ['__lang_id__' => 3, 'title' => 'Third Title', 'content' => 'Third Content'];
            
            $content->ContentLangs()->create($postThird);
           
            Model\ContentLangSoftDelete::all()->each(function($item){
                
                if ((integer)$item->__lang_id__ === 3) {
                    
                    $item->delete();
                }            
            });            
                        
            $wrapper = $this->wrapper->createNew($content,3,1);            
           
            $this->assertEquals($wrapper->wanted(1)->title, $postFirst['title']);           
          
            $this->assertEquals($wrapper->wanted(2)->title, $postSecond['title']);
            // var_dump(\DB::getQueryLog());
            $this->assertEquals($wrapper->wanted(3)->force()->title, null);
            
            $this->assertEquals($wrapper->wanted(3)->title, $postFirst['title']);
            
            $this->assertEquals($wrapper->title, $postFirst['title']); 
           
        }
}
