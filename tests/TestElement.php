<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Element;
use PHPUnit_Framework_TestCase as UnitTest;
/**
 * a test class for \Muratsplat\Multilang\Picker
 * 
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestElement  extends UnitTest {

    /**
     *
     * @var Muratsplat\Multilang\Picker 
     */
    protected $obj = null;
       
        /*
         * testing IoC Container
         */
        public function testFirst() {
            
            $picker = new Element();
            
        }
        
        public function setUp() {
            parent::setUp();
         
            
            $this->obj = new Element();
        }
        
        
        public function tearDown() {
        
            parent::tearDown();        
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
            
            try {
                
                $picker->test1;  
                
            } catch (\Muratsplat\Multilang\Exceptions\ElementUndefinedProperty $ex) {
                
                $this->assertNull(null);
            }
            
            try {
                
                unset($picker->notExisted); 
                
            } catch (\Muratsplat\Multilang\Exceptions\ElementUndefinedProperty $ex) {
                
                $this->assertNull(null);
            }
                       
        }

        
        public function testSimpleEmptyValueNonMultiLang() {
            
            $this->obj->content = "bla bla bla";
            
            $this->obj->content = "";
            
             try {
                
                $this->obj->content;
                
                $this->assertTrue(true);
                
            } catch (\Muratsplat\Multilang\Exceptions\ElementUndefinedProperty $ex) {
                
                $this->assertTrue(false);
            }            
            
        }
        
        public function testSimpleEmptyValueMultiLang() {
            
            $this->obj->content = "bla bla bla";
            
            $this->obj->setMultilang(true);
            
            // now  Overloaded properties must be deleted
            $this->obj->content = "";                      
            
            try {
                $this->obj->content;
                
                $this->assertTrue(false);
                
            } catch (\Muratsplat\Multilang\Exceptions\ElementUndefinedProperty $ex) {
                
                $this->assertTrue(true);
            }            
            
        } 
        
        public function testAllkeyNull() {
            
            $this->obj->setMultilang(true);
            
            $this->obj->content = "bla bla bla";
             
            $this->obj->title = "bilmem ne";
            
            // uptating
            
            $this->obj->content = null;
             
            $this->obj->title = null;
           
            $this->assertTrue($this->obj->allkeyNull());          
            
        }
        
        public function testAllkeyNull2() {
            
            $this->obj->setMultilang(true);
            
            $this->obj->content = "bla bla bla";
             
            $this->obj->title = "bilmem ne";
            
            // uptating
            
            $this->obj->content = "";
             
            $this->obj->title = "";
           
            $this->assertTrue($this->obj->allkeyNull());
        }
        
        public function testNewElement() {
            
            $this->assertInstanceOf('Muratsplat\Multilang\Element', $this->obj->newElement());
        }
        
        public function testToArray() {
            
            $this->obj->foo = 'bar';
            
            $this->obj->title = "Lorem";
            
            $testArray = ['foo' => 'bar', 'title' => 'Lorem'];
            
            $this->assertEquals($testArray, $this->obj->toArray());
        }
        
        public function testToArrayForMultilang() {
            
            $this->obj->foo = 'bar';
            
            $this->obj->title = "Lorem";
            
            $this->obj->setMultilang(true);
            
            $this->obj->setId(1);
            
            $testArray = [1=> ['foo' => 'bar', 'title' => 'Lorem']];
            
            $this->assertEquals($testArray, $this->obj->toArray());
        }
        
        
        public function testAllkeyNullOnNonMultilang() {
            
            $this->obj->setMultilang(false);
            
            $this->obj->content = "bla bla bla";
             
            $this->obj->title = "bilmem ne";
            
            $this->assertFalse($this->obj->allkeyNull());
            // uptating
            
            $this->obj->content = null;
             
            $this->obj->title = null;
           
            $this->assertTrue($this->obj->allkeyNull());          
            
        }
            
}
