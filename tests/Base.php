<?php namespace Muratsplat\Multilang\Tests;

require '../../../bootstrap/autoload.php';
use Illuminate\Foundation\Testing\TestCase as UnitTest;


/**
 * Test Base Class
 *
 * @author Murat Ödünç <murat.asya@gmail.com>
 */
class Base  extends UnitTest {
            

        
        public function createApplication() {
            
            $unitTesting = true;
            $testEnvironment = 'testing';
            
            return require '../../../bootstrap/start.php';
        }
        
        public function testExample() {
            
            $this->assertTrue(true);
        }
        
       
          

}
