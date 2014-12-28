<?php namespace Muratsplat\Multilang\Tests;

use Illuminate\Support\Collection;
//use Illuminate\Config\Repository;

use Muratsplat\Multilang\Picker;
//use Muratsplat\Multilang\Tests\Base;
use Muratsplat\Multilang\Element;
use Muratsplat\Multilang\MultiLang;
//use Muratsplat\Multilang\Validator;
use Muratsplat\Multilang\Tests\Model\Content;
use Muratsplat\Multilang\Tests\Model\ContentLang;
//use Muratsplat\Multilang\Tests\Migrate\Contents as migrateContent;

// for testing CRUD ORM jobs..
use Muratsplat\Multilang\Tests\MigrateAndSeed;

use \Mockery as m;
//use Illuminate\Validation\Validator as laravelValidator;


/**
 * a test class for \Muratsplat\Multilang\MultiLang
 *
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestMultilang extends MigrateAndSeed {    
    
    /**
     *
     * @var Muratsplat\Multilang\MultiLang
     */
    private $multiLang;
    
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
        
        'enable' => 1, 
        'visible' => 1, 
        'content@1' => 'test',
        'title@1' => 'Title test',

        'content@2' => 'test',
        'title@2' => 'Title test',
   
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
         * @return \Mockery\MockInterface
         */
        protected function getMockedConfig() {
            
            return m::mock('Illuminate\Config\Repository');
                           
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
            $mockedConfig->shouldReceive('get', 'multilang::prefix')->andReturn('@'); 
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $validator->shouldReceive('make')->andReturn(true);
            $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator);

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
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
              
            $validator->shouldReceive('make')->andReturn(true);
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),               
                    $mockedConfig, 
                    $messageBag,
                    $validator);

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
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);            
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator);

            $this->assertTrue($multiLang->create($this->multilangPost, new Content()));            
            
            $this->assertEquals(1, Content::all()->count());            
            
            $this->assertEquals(2, count(Content::find(1)->ContentLangs));           
        }
        
        public function testCreateEmptyPostData() {
            
            $mockedConfig = $this->getMockedConfig();            
            $mockedConfig->shouldReceive('get', 'multilang::prefix')->andReturn('@');
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);            
            
            $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
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
        
        public function testSimpleUpdateWithNonMultilang() {
            
            $mockedConfig = $this->getMockedConfig();            
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator);
             
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
            
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator);

             
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
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator);             
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
            
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(), new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator);
             
            $created = new Content();                     
            $created->save();            
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
            
            $messageBag = $this->getMockedMessageBag();            
            $validator = $this->getMockedValid();
            
            $mockedConfig->shouldReceive('get')->andReturn('Lang');
            
            $validator->shouldReceive('make')->andReturn(true);
            
             $multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element(), $mockedConfig),
                    $mockedConfig, 
                    $messageBag,
                    $validator);
                      
            $multiLang->create($this->multilangPost, new Content);            
            
            $this->assertTrue($multiLang->delete(Content::find(1)));
            
            $this->assertNull(Content::find(1));
            $this->assertEquals(0, ContentLang::all()->count());    
        }
}
