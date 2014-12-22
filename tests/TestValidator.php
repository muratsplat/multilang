<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Tests\Base;
//use Muratsplat\Multilang\Element;
//use Muratsplat\Multilang\MultiLang;
use Muratsplat\Multilang\Validator;

use Illuminate\Validation\Factory;
//use Illuminate\Support\Collection;
//use Illuminate\Config\Repository as Config;
//use Muratsplat\Multilang\Tests\Model\Content;
use \Mockery as m;


/**
 * a test class for \Muratsplat\Multilang\MultiLang
 *
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestValidator extends Base {    
    
    /**
     *
     * @var Muratsplat\Multilang\MultiLang
     */
    private $multiLang;
    
    
    /**
     *
     * @var Muratsplat\Multilang\Validator 
     */
    private $validator;
   
        
        public function tearDown() {
        
            parent::tearDown();        
            
            m::close();
        }
        
        public function setUp() {
            parent::setUp();
            
            $mockedConfig = m::mock('Illuminate\Config\Repository','Illuminate\Config\LoaderInterface');
            $messageBag = m::mock('Illuminate\Support\MessageBag');
            $laraValidator = m::mock('Illuminate\Validation\Factory');
            
            $this->validator = new Validator($messageBag, $laraValidator, $mockedConfig);            

        }

        public function testCheckMainImplement() {

            
        }

    
    
    
    
    
    
    
}
