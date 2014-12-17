<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Tests\Base;


/**
 * a test class for \Muratsplat\Multilang\Picker
 *
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */
class TestPicker  extends Base {
    
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
     *
     * @var Muratsplat\Multilang\Picker 
     */
    protected $obj = null;
       
        /*
         * testing IoC Container
         */
        public function testFirst() {
            
            $picker = new Picker(new \Illuminate\Support\Collection());
            
        }
        
        public function setUp() {
            parent::setUp();
            
            $this->obj = new Picker(new \Illuminate\Support\Collection());
                  
        }   
       
        public function testImport() {
            
            $this->assertEquals(5, $this->obj->import($this->rawPost));
            // testing update methods..
            $this->assertEquals(5, $this->obj->import($this->rawPost));
                    
            // testing update methods..
            $this->assertEquals(5, $this->obj->import($this->rawPost));           
            
        }
        
        public function testCheckElemen1() {
            
            $this->obj->import($this->rawPost);
            // retu
            $firstElem = $this->obj->getCollection()->first();

            $this->assertEquals($firstElem->enable, 1);
            
            try {
                
                $this->assertEquals($firstElem->title, "Foo Türkçe" );
                
                $this->assertTrue(false);
                
            } catch (\Muratsplat\Multilang\Exceptions\ElementUndefinedProperty $e) {
                
                $this->assertTrue(true);               

            }
                       
        }
        
        
}
