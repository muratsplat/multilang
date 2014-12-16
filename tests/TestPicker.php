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
            
            $this->obj = $picker =  new Picker(new \Illuminate\Support\Collection());
        }
        
        
        public function testOverloading() {
            
            $picker = $this->obj;
            
            $picker->test1 = 'test1';
            
            $picker->test2 = 'test2';
            
            $this->assertEquals($picker->test1, 'test1');
            
            $this->assertEquals($picker->test2, 'test2');
            
            $this->assertTrue(isset($picker->test1));
            
            $this->assertFalse(isset($picker->test99));
            
            unset($picker->test1);
            
            $this->assertNull($picker->test1);      
            
        }
        
        public function testImport() {
            
          $this->obj->import($this->rawPost);
          
          
                
            
        }
        
        
}
