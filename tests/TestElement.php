<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Tests\Base;
use Muratsplat\Multilang\Element;
/**
 * a test class for \Muratsplat\Multilang\Picker
 *
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */
class TestElement  extends Base {

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

       
}