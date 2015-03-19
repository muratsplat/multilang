<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Picker;
use PHPUnit_Framework_TestCase as UnitTest;
use Muratsplat\Multilang\Element;

use Mockery as m;

/**
 * a test class for \Muratsplat\Multilang\Picker
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestPicker  extends UnitTest {
    
    /*
     * Simple Post Data
     * 
     */
    private $rawPost  = array(
        
        "enable"    => 1,
        "visible"   => 0,
        
        'title@1'   => "Foo English",
        'content@1' => "Simple example of content in English",
        
        'title@2'   => 'Foo Türkçe',
        'content@2' => 'Türkçe bir içerik langur lungur bir yoğurt',
        
        "title@3"   => 'здравствуйте',
        "content@3" => 'Путинхороший человек. Он любит русские , я думаю, россияне любят его.'      
    );
    
    /**
     * Another Example
     * 
     * @var type 
     */
    private $anotherPost = array( 
        //1
        'parent_id'     => '0',
        'enable'        => "1",
        'slug'          => '',
        'alias'         => '',
        'image_url'     => 'image_url', 
         // 2
        'name@1' => 'Türkçe Name',
        'tags@1' => 'TÜrkçe Tags',
        'meta_desc@1' => 'Türkçe Tanımlamalar',
        'meta_keys@1' => 'Türkçe Anahtarlar',
        'about@1' => 'Türkçe Hakkında',          
         // 3
        'name@2' => '',
        'tags@2' => 'ingilizce Tags',
        'meta_desc@2' => 'İngilizce Tanımlamalar',
        'meta_keys@2' => 'İngilizce Anahtarlar',
        'about@2' => 'İngilizce Hakkında',
         // 4
        'name@3' => 'Kategori İsmi Almanca',
        'tags@3' => 'Almanca Tags',
        'meta_desc@3' => 'Almanca Tanımlamalar',
        'meta_keys@3' => 'Almanca Anahtarlar',
        'about@3' => 'Almanca Hakkında',
         //5
        'name@4' => 'Kategori İsmi fransızca',
        'tags@4' => 'Fransızca Tags',
        'meta_desc@4' => 'Fransıca Tanımlamalar',
        'meta_keys@4' => 'Fransıca Anahtarlar',
        'about@4' => 'Fransıca Hakkında',
         // 6
        'name@5' => 'Kategori İsmi rusça',
        'tags@5' => 'Rusça Tags',
        'meta_desc@5' => 'Rusça Tanımlamalar ',
        'meta_keys@5' => 'Rusça Anahtarlar',
        'about@5' => 'Rusça Hakkında',
         // 7
        'name@6' => 'Kategori İsmi arapça',
        'tags@6' => 'Arapça Tags',
        'meta_desc@6' => 'Arapça Tanımlamalar',
        'meta_keys@6' => 'Arapça Anahtarlar',
        'about@6' => 'Arapça Hakkında',            

       );
       
    /**
     *
     * @var Muratsplat\Multilang\Picker 
     */
    protected $obj = null;
       
        /*
         * testing IoC Container
         */
        public function testFirst() {
            
            $mockedConfig = m::mock('Illuminate\Config\Repository');
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            
            
            new Picker(new \Illuminate\Support\Collection(), new Element(), $mockedConfig);            
        }
        
        public function setUp() {
            parent::setUp();
            
            $mockedConfig = m::mock('Illuminate\Config\Repository');
            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
            
            $this->obj = new Picker(new \Illuminate\Support\Collection(), new Element(), $mockedConfig);
                  
        }   
       
        public function testImport() {
           
           $this->assertEquals(4, $this->obj->import($this->rawPost)->getCollection()->count());
           // testing update methods..
           $this->assertEquals(4, $this->obj->import($this->rawPost)->getCollection()->count());
          
        }
        
        public function testCheckElemen1() {
            
            $this->obj->import($this->rawPost);
            // retu
            $firstElem = $this->obj->getCollection()->last();

            $this->assertEquals($firstElem->enable, 1);
            
            try {
                
                $this->assertEquals($firstElem->title, "Foo Türkçe" );
                
                $this->assertTrue(false);
                
            } catch (\Muratsplat\Multilang\Exceptions\ElementUndefinedProperty $e) {
                
                $this->assertTrue(true);
            }
                       
        }
        
//         "enable"    => 1,
//        "visible"   => 0,
//        
//        'title@1'   => "Foo English",
//        'content@1' => "Simple example of content in English",
//        
//        'title@2'   => 'Foo Türkçe',
//        'content@2' => 'Türkçe bir içerik langur lungur bir yoğurt',
//        
//        "title@3"   => 'здравствуйте',
//        "content@3" => 'Путинхороший человек. Он любит русские , я думаю, россияне любят его.' 
        public function testExamplePostData() {
           
            $this->obj->import($this->rawPost);
          
            $this->obj->getCollection();
            
            $this->assertEquals($this->rawPost['enable'], $this->obj->getCollection()->last()->enable);
            $this->assertEquals($this->rawPost['visible'], $this->obj->getCollection()->last()->visible);
           
            $this->assertEquals($this->rawPost['title@1'], $this->obj->getById(1)->title);
            $this->assertEquals($this->rawPost['content@1'], $this->obj->getById(1)->content);
            
            $this->assertEquals($this->rawPost['title@2'], $this->obj->getById(2)->title);
            $this->assertEquals($this->rawPost['content@2'], $this->obj->getById(2)->content);
            
            $this->assertEquals($this->rawPost['title@3'], $this->obj->getById(3)->title);
            $this->assertEquals($this->rawPost['content@3'], $this->obj->getById(3)->content);          
        }
        
        public function testExampleUpdate() {
            
            /*
             * Simple Post Data for updating
             * 
             */
            $rawPostUpdate  = array(

                "enable"    => 1,
                "visible"   => 0,

                'title@1'   => "Foo English",
                'content@1' => "",

                'title@2'   => '',
                'content@2' => 'Türkçe bir içerik langur lungur bir yoğurt',

                "title@3"   => '',
                "content@3" => ''      
            );

            $this->obj->import($this->rawPost);
            
            $this->obj->import($rawPostUpdate);
            //  after upate the number of items is 4!
            $collection = $this->obj->getCollection();
            
            $this->assertEquals(3, count($collection));           
        }
        
        public function testExampleUpdate2() {
            
            /*
             * Simple Post Data for updating
             * 
             */
            $rawPostUpdate  = array(

                "enable"    => 1,
                "visible"   => 0,

                'title@1'   => "Foo English",
                'content@1' => "",

                'title@2'   => '',
                'content@2' => 'Türkçe bir içerik langur lungur bir yoğurt',

                "title@3"   => '',
                "content@3" => ''      
            );

            $this->obj->import($rawPostUpdate);
           
            //  after upate the number of items is 4!
            $collection = $this->obj->getCollection()->all();
                                  
            $this->assertEquals(3, count($collection));           
        }
        
        public function testExampleUpdate3() {
            
            /*
             * Simple Post Data for updating
             * 
             */
            $rawPostUpdate  = array(

                'title@1'   => "Foo English",
                'content@1' => "",

                'title@2'   => '',
                'content@2' => 'Türkçe bir içerik langur lungur bir yoğurt',

                "enable"    => 1,
                "visible"   => 0,
                
                "title@3"   => '',
                "content@3" => ''      
            );

            $this->obj->import($rawPostUpdate);
           
            //  after upate the number of items is 3!
            $collection = $this->obj->getCollection()->all();
                                  
            $this->assertEquals(3, count($collection));           
        }
        
        public function testExampleUpdate4() {
            
            /*
             * Simple Post Data for updating
             * 
             */
            $rawPostUpdate  = array(

                'title@1'   => "Foo English",
                'content@1' => "",

                'title@2'   => '',
                'content@2' => 'Türkçe bir içerik langur lungur bir yoğurt',

                              
                "title@3"   => '',
                "content@3" => ''      
            );

            $this->obj->import($rawPostUpdate);
          
            //  after upate the number of items is 2!
            $collection = $this->obj->getCollection()->all();
           
            $this->assertEquals(2, count($collection));           
        }
        
        public function testExampleUpdate5() {
            
            /*
             * Simple Post Data for updating
             * 
             */
            $rawPostUpdate  = array(

                'title@1'   => "Foo English",
                'content@1' => "",

                'title@2'   => '',
                'content@2' => 'Türkçe bir içerik langur lungur bir yoğurt',

                              
                "title@3"   => '',
                "content@3" => ''      
            );
            
            $this->obj->import($rawPostUpdate);
            
            $this->assertEquals(2, count($this->obj->getCollection()->all()));   
            
            $this->obj->import($this->rawPost);
          
            $collection = $this->obj->getCollection()->all();
                                  
            $this->assertEquals(4, count($collection));           
        }
        
        public function testExampleUpdate6() {
            
            /*
             * Simple Post Data for updating
             * 
             */
            $rawPostUpdate  = array(

                'foo'       => 'bar',
                'Footest'   => 'Fıı',
            );
            
            $this->obj->import($this->rawPost);           
            
            $this->assertEquals(4, count($this->obj->getCollection()->all()));   
            
            $this->obj->import($rawPostUpdate);
          
            $collection = $this->obj->getCollection()->all();
              
            $this->assertEquals(1, count($collection));           
        }
        
        public function testExampleCreateAndUpdateAgain() {
            
            $this->obj->import($this->anotherPost);
            
            $collaction = $this->obj->getCollection();
            
            $this->assertEquals(7,$collaction->count());
            
            $this->assertEquals('Fransızca Tags', $this->obj->getById(4)->tags);
            
            $this->assertEquals('Türkçe Tanımlamalar', $this->obj->getById(1)->meta_desc);
            
            $this->assertEquals('Arapça Hakkında', $this->obj->getById(6)->about);
            
            $this->assertCount(1, $this->obj->getNonMultilang());
            
            $this->assertCount(6, $this->obj->getMultilang());
        }
        
        public function testGetById() {
            
            $this->obj->import($this->anotherPost);
            
            $frenchs = $this->obj->getById(4);

            //  'name@4' => 'Kategori İsmi fransızca',
            //  'tags@4' => 'Fransızca Tags',
            //  'meta_desc@4' => 'Fransıca Tanımlamalar',
            //  'meta_keys@4' => 'Fransıca Anahtarlar',
            //  'about@4' => 'Fransıca Hakkında',
            
            $this->assertEquals($this->anotherPost['name@4'], $frenchs->name);
            
            $this->assertEquals($this->anotherPost['tags@4'], $frenchs->tags);
            
            $this->assertEquals($this->anotherPost['meta_desc@4'], $frenchs->meta_desc);
            
            $this->assertEquals($this->anotherPost['meta_keys@4'], $frenchs->meta_keys);
            
            $this->assertEquals($this->anotherPost['about@4'], $frenchs->about);
        }
        
        public function testNonMultilangToArray() {
            
            $this->obj->import($this->rawPost);
            
            $oneMustBe = [ "enable"    => 1,"visible"   => 0,];
                
            $this->assertEquals($oneMustBe,$this->obj->getNonMultilangToArray());            
            
        }
        
        public function testMultilangToArray() {
            
            $this->obj->import($this->rawPost);
            
            $shouldBe = [
                    ['title'  => "Foo English",
                    'content' => "Simple example of content in English",
                    '__lang_id__' => 1],
                    
                    ['title'   => 'Foo Türkçe',
                    'content' => 'Türkçe bir içerik langur lungur bir yoğurt',
                    '__lang_id__' => 2],
                        
                    ["title"   => 'здравствуйте',
                    "content" => 'Путинхороший человек. Он любит русские , я думаю, россияне любят его.',
                    '__lang_id__' => 3],
                ];
            
            $this->assertEquals($shouldBe, $this->obj->getMultilangToArray());           
        }
        
        public function testIsPostMultilang() {
            
            $this->obj->import($this->rawPost);
            
            $this->assertTrue($this->obj->isPostMultiLang());            
            
        }
        
        public function atestOnlyNonMultilangPost() {
            
            $post = ['doo' => 'bar', 'sede' => '1'];
            
            $resault = $this->obj->import($post, true);
            
            $this->assertTrue($resault);
            
            $this->assertEquals(2, count($this->obj->getCollection()->all()));   
                      
        }
        
        public function testLostedExistLangThatsBug() {
                       
            $postFirst = [ 
                
                'enable'    => 1, 
                'visible'   => 1,
                'content@1' => 'Content 1',
                'title@1'   => 'Title 1',
                'content@2' => 'Content 2',
                'title@2'   => 'Title 2',
                ];
            
            $this->obj->import($postFirst);
                     
            $this->assertEquals($postFirst['enable'], $this->obj->getNonMultilang()->last()->enable);
            
            $this->assertEquals($postFirst['visible'], $this->obj->getNonMultilang()->last()->visible);
            
            $postFirst['content@1'] = '';
            
            $postFirst['title@2']   = '';
            
            $postFirst['enable']    = 99;
            
            $postFirst['visible']   = 88;            
                      
            $this->obj->import($postFirst);
                        
            $this->assertEquals($postFirst['enable'], $this->obj->getNonMultilang()->last()->enable);
            $this->assertEquals($postFirst['visible'],$this->obj->getNonMultilang()->last()->visible);
            
            $multilangElem = $this->obj->getById(1);
            $this->assertNotNull($multilangElem); // A bug was founded !! 
                    
        }
        
        
        public function tearDown() {
        
            parent::tearDown();        
            
            m::close();
        }
        
}
