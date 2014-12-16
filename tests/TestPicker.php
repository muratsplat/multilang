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
         * testing IoC Container
         */
        public function testFirst() {
            
            $picker = \App::make('pickerML');
            
            $this->assertInstanceOf('Muratsplat\Multilang\Picker', $picker);
        }
        
        
        public function testOverloading() {
            
            $picker = \App::make('pickerML');
            
            $picker->test1 = 'test1';
            
            $picker->test2 = 'test2';
            
            $this->assertEquals($picker->test1, 'test1');
            
            $this->assertEquals($picker->test2, 'test2');
            
            $this->assertTrue(isset($picker->test1));
            
            $this->assertFalse(isset($picker->test99));
            
            unset($picker->test1);
            
            $this->assertNull($picker->test1);
            
            
            
            
            
        }
        
        
}
