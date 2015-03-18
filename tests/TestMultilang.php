<?php namespace Muratsplat\Multilang\Tests;

use Illuminate\Support\Collection;
use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Element;
use Muratsplat\Multilang\MultiLang;
use Muratsplat\Multilang\Wrapper;
use Muratsplat\Multilang\CheckerAttribute;
// for only test
use Muratsplat\Multilang\Tests\Model\Content;
use Muratsplat\Multilang\Tests\Model\ContentLang;
// for testing CRUD ORM jobs..
use Muratsplat\Multilang\Tests\MigrateAndSeed;
use Muratsplat\Multilang\Tests\CreateContentAndLangTraitForTest;

use \Mockery as m;

/**
 * a test class for \Muratsplat\Multilang\MultiLang
 * 
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestMultilang extends MigrateAndSeed {
    
    use CreateContentAndLangTraitForTest;
    
    /**
     * an example data for tests
     *
     * @var array 
     */
    private $nonMultilangPost = array(
        
        'enable' => 1, 
        'visible' => 1, 
          
    );
    
    /**
     * an example data for tests
     * 
     * @var array 
     */
    private $multilangPost = array(
        '_token'    =>'wqkjf9012r0f128f12f',
        'enable'    => 1, 
        'visible'   => 1, 
        'content@1' => 'Content İki',
        'title@1'   => 'Title Bir',
        'content@2' => 'Content İki',
        'title@2'   => 'Title İki',
   
    );
    
        public function tearDown() {
        
            parent::tearDown();        
            
            m::close();
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
         * @return \Illuminate\Events\Dispatcher
         */
        protected function getEvents() {
            
            return $this->app->make('events');
        }

        public function testCheckMainImplement() {
            
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get', 'multilang::prefix')->andReturn('@');
            $messageBag = $this->getMockedMessageBag();            
            $validator  = $this->getMockedValid();
            $wrapper    = $this->getWrapper();
                      
            $validator->shouldReceive('make')->andReturn(true);
            $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper);

            $this->assertTrue($multiLang->create(['visible'=>1], new Content()));
    
        }
        
        public function testCheckContentsMigration() {
            
            $content = new Content($this->nonMultilangPost);
            
            $this->assertTrue($content->save());
            
            $this->assertEquals(1, $content->enable);
            
            $this->assertEquals(1, $content->visible);            
            
        }
        
        public function testCheckContentLangRelation() {
            
            $content = new Content($this->nonMultilangPost);
            
            $content->save();
            
            $this->assertInstanceOf('Muratsplat\Multilang\Tests\Model\ContentLang', $content->ContentLangs()->getRelated());           
        }
        
        public function testTryCRUDContentLang() {        
            
            $content = new Content($this->nonMultilangPost);
            
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
            $mockedConfig->shouldReceive('get', 'multilang::prefix')->andReturn('@');
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            $wrapper = $this->getWrapper();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
              
            $validator->shouldReceive('make')->andReturn(true);
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),               
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper);

            $this->assertTrue($multiLang->create($this->nonMultilangPost, new Content()));
            
            $this->assertEquals(1, Content::all()->count());
            
            $this->assertEquals($this->nonMultilangPost['enable'], Content::find(1)->enable);
            $this->assertEquals($this->nonMultilangPost['visible'], Content::find(1)->visible);
        }
        
        public function testWithMultilangPost() {
            
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            $wrapper = $this->getWrapper();
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);            
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper);

            $this->assertTrue($multiLang->create($this->multilangPost, new Content()));            
            
            $this->assertEquals(1, Content::all()->count());            
            
            $this->assertEquals(2, count(Content::find(1)->ContentLangs));
            
            $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $multiLang->getMainModel());
            
            $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasOneOrMany', $multiLang->getLangModels());
        }
        
        public function testCreateEmptyPostData() {
            
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get', 'multilang::prefix')->andReturn('@');
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            $wrapper = $this->getWrapper();
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);            
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper);

            $post = [];
            try {
                
                $multiLang->create($post, new Content());
                
                $this->assertTrue(false);
                
            } catch (\Muratsplat\Multilang\Exceptions\MultilangPostEmpty $ex) {
                
                return $this->assertTrue(true);

            }            
        }
        
        public function testSimpleUpdateWithNonMultilang() {
            
            $mockedConfig = $this->getMockedConfig();            
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            $wrapper = $this->getWrapper();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper);
             
            $created = new Content();
            
            $created->visible = 0;
            
            $created->enable = 0;
            
            $created->save();
             
            $this->assertTrue($multiLang->update($this->nonMultilangPost, $created));
            
            $this->assertEquals(1, Content::find(1)->visible);
                        
            $this->assertEquals(1, Content::find(1)->enable);
            
        }
        
        public function testSimpleUpdateWithMultilang() {
            
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            $wrapper = $this->getWrapper();
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper);

             
            $created = new Content();
            
            $created->visible = 0;
            
            $created->enable = 0;
            
            $created->save();
             
            $this->assertTrue($multiLang->update($this->multilangPost, $created));
            
            $this->assertEquals(1, Content::find(1)->visible);
            
            $this->assertEquals(1, Content::find(1)->enable);
            
            $this->assertEquals(2, count(Content::find(1)->ContentLangs));
            
            // let!s update again.
            $this->assertTrue($multiLang->update($this->multilangPost, $created));
            
            $this->assertEquals(2, count(Content::find(1)->ContentLangs));
            
           // let's update diffrent post            
            unset($this->multilangPost['content@2']);           
            unset($this->multilangPost['title@2']);
            $this->assertTrue($multiLang->update($this->multilangPost, $created));
            
            $this->assertEquals(1, count(Content::find(1)->ContentLangs));     
        }
        
        public function testUpdateWithNonMultilangAndMultilang() {
            
            $mockedConfig = $this->getMockedConfig();            
            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            $wrapper = $this->getWrapper();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper);             
            $created = new Content();                    
            $created->save();             
            $this->assertTrue($multiLang->update($this->nonMultilangPost, $created));                     
            
            // let!s update again.
            $this->assertTrue($multiLang->update($this->multilangPost, $created));            
            $this->assertEquals(2, count(Content::find(1)->ContentLangs));
           
        }
        
        public function testUpdateWithAndMultilang1() {
            
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            $wrapper = $this->getWrapper();
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper);
             
            $created = new Content($this->nonMultilangPost);                     
            
            $this->assertTrue($created->save());            
            $this->nonMultilangPost['content@1'] = 'test Content';             
            $this->assertTrue($multiLang->update($this->nonMultilangPost, $created));            
            $this->assertEquals(1, count(Content::find(1)->ContentLangs));
            
            // let!s update again.
            $this->assertTrue($multiLang->update($this->multilangPost, $created));            
            $this->assertEquals(2, count(Content::find(1)->ContentLangs));           
        }
        
        public function testDeleteWithAndMultilang() {
            
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            $wrapper = $this->getWrapper();
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper);
                      
            $multiLang->create($this->multilangPost, new Content);            
            
            $this->assertTrue($multiLang->delete(Content::find(1)));
            
            $this->assertNull(Content::find(1));
            $this->assertEquals(0, ContentLang::all()->count());    
        }
        
        public function testMultilangAndWrapperSimple() {
            
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    new Wrapper($mockedConfig, $this->getCheckerAttribute()));
            
            $multiLang->create($this->multilangPost, new Content);
            
            $wrapper = $multiLang->makeWrapper(Content::find(1), 2,1);            
            
            $this->assertEquals($this->multilangPost['visible'], $wrapper->visible);                      
            $this->assertEquals($this->multilangPost['title@2'], $wrapper->title);            
            $this->assertEquals($this->multilangPost['content@2'], $wrapper->content);       
        }
        
         public function testMultilangAndWrapperSimpleWithEloquentCollection() {
            
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    new Wrapper($mockedConfig,$this->getCheckerAttribute()));
           
            $this->createContentWithLanguages();
            
            $wrapperCollection = $multiLang->makeWrapper(Content::all(), 2,1);
            
            $this->assertInstanceOf('Illuminate\Support\Collection', $wrapperCollection);
            
            foreach ($wrapperCollection as $v) {
                
                $v->enable;
                $v->visible;
                $v->title;
                $v->content;            
            } 
        }
        
        public function createContentWithLanguages() {
            
            for ($i = 1; $i < 6; $i++) {
                
                Content::create(['visible'=> 1, 'enable' =>1]);
                
            }
            
            foreach (Content::all() as $v) {
                
                for ($i = 1; $i < 11; $i++) {
                    
                    $v->langModels()->create(['content' => str_random(),'title' => str_random(), '__lang_id__' => $i]);                    
                    
                }                
            }
                       
        }
        
        public function testUpdateWithAndMultilangEmptyElements() {
            
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            $wrapper = $this->getWrapper();
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             
            $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper);
             
            $this->createContentWithLanguages();
          
            $this->assertCount(5, Content::all());
            
            $this->assertCount(50, ContentLang::all());
         
            $updated = Content::find(4);
            $post = [
                'title@1'   => 'Foo Update',
                'visible'   => '',
                'enable'    => '',               
            ];
              
            $this->assertTrue($multiLang->update($post, $updated));            
            $updatedLang = ContentLang::query()->where('content_id', 4)->where('__lang_id__', 1)->get()->first();            
            $this->assertEquals($post['title@1'], $updatedLang->title);            
            
            $post2 = [
                'title@1'   => 'Foo Update İki',
                'content@1' => 'Content Bla bla',
                'visible'   => '',
                'enable'    => '',               
            ];
            
            $this->assertTrue($multiLang->update($post2, $updated));
            $updatedLang1 = ContentLang::query()->where('content_id', 4)->where('__lang_id__', 1)->get()->first();            
            $this->assertEquals($post2['title@1'], $updatedLang1->title);            
            $this->assertEquals($post2['content@1'], $updatedLang1->content);                     
          }
        
       
          
          public function testCollectionsAsParameter() {
              
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            $wrapper = new Wrapper($mockedConfig, $this->getCheckerAttribute());
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             
            $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper
                    );
            
            $this->assertTrue($this->createContent(3));

            /* first */
            $postFirst = ['__lang_id__' => 1, 'title' => 'First Title', 'content' => 'First Content'];
            Content::find(1)->ContentLangs()->create($postFirst);

            /* second */
            $postSecond = ['__lang_id__' => 1, 'title' => 'Second Title', 'content' => 'Second Content'];            
            Content::find(2)->ContentLangs()->create($postSecond);

            /* third */            
            $postThird = ['__lang_id__' => 1, 'title' => 'Third Title', 'content' => 'Third Content'];
            Content::find(3)->ContentLangs()->create($postThird);
            
            $wrap = $multiLang->makeWrapper(Content::all(),1,1);
            
            $this->assertCount(3, $wrap);
            
            foreach ($wrap as  $v) {
                
                $v->title;
                
                $v->content;
            }       
        }
        
        public function testEventControlOnMultilang() {
              
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            $wrapper = new Wrapper($mockedConfig, $this->getCheckerAttribute());
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);            
             
             // setting laravel events object
            MultiLang::setEventDispatcher($this->app['events']);
            
            $this->app['multilang'] =  new MultiLang(
            
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator,
                    $wrapper
                    );
                       
                         
            $this->assertTrue($this->createContent(1));

            $content = Content::find(1);
            /* first */
            $postFirst = ['__lang_id__' => 1, 'title' => 'First Title', 'content' => 'First Content'];
            $content->ContentLangs()->create($postFirst);

            /* second */
            $postSecond = ['__lang_id__' => 2, 'title' => 'Second Title', 'content' => 'Second Content'];            
            $content->ContentLangs()->create($postSecond);

            /* third */            
            $postThird = ['__lang_id__' => 3, 'title' => 'Third Title', 'content' => 'Third Content'];
            $content->ContentLangs()->create($postThird);
            
            /* setting wanted lang by using event */    
            $this->app['events']->listen('multilang.wrapper.creating',function(MultiLang $event) {
                
                $event->getWrapperInstance()->setWantedLang(1);                
            }); 
            /* first */
            $wrapper1 = $this->app['multilang']->makeWrapper($content);            
            $this->assertEquals($postFirst['title'], $wrapper1->title );
            
            
            /* setting wanted lang by using event */    
            $this->app['events']->listen('multilang.wrapper.creating',function(MultiLang $event) {
                
                $event->getWrapperInstance()->setWantedLang(2);                
            });
            /* second */
            $wrapper2 = $this->app['multilang']->makeWrapper($content);            
            $this->assertEquals($postSecond['title'], $wrapper2->title );
            
            /* setting wanted lang by using event */    
            $this->app['events']->listen('multilang.wrapper.creating',function(MultiLang $event) {
                
                $event->getWrapperInstance()->setWantedLang(3);                
            });
            /* third */
            $wrapper3 = $this->app['multilang']->makeWrapper($content);            
            $this->assertEquals($postThird['title'], $wrapper3->title );           
        } 
        
        /**
         * To get CherkerAttribute Object
         * 
         * @return CheckerAttribute
         */
        private function getCheckerAttribute() {
            
            $config = $this->getMockedConfig();
            
            $config->shouldReceive('get', 'multilang::cachePrefix')->andReturn('/test/multilang');
            
            return new CheckerAttribute(
                    
                    $this->app['db']->connection()->getSchemaBuilder(), 
                    
                    $this->app['cache'],                    
                    $config                    
                    );
        }
           
}
