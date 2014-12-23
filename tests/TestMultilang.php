<?php namespace Muratsplat\Multilang\Tests;

use Illuminate\Support\Collection;
use Illuminate\Config\Repository as Config;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Tests\Base;
use Muratsplat\Multilang\Element;
use Muratsplat\Multilang\MultiLang;
use Muratsplat\Multilang\Validator;
use Muratsplat\Multilang\Tests\Model\Content;
use \Mockery as m;


/**
 * a test class for \Muratsplat\Multilang\MultiLang
 *
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestMultilang extends Base {    
    
    /**
     *
     * @var Muratsplat\Multilang\MultiLang
     */
    private $multiLang;
   
        
        public function tearDown() {
        
            parent::tearDown();        
            
            m::close();
        }
        
        public function setUp() {
            parent::setUp();
            
            $mockedConfig = m::mock('Illuminate\Config\Repository','Illuminate\Config\LoaderInterface');
            
            $messageBag = m::mock('Illuminate\Support\MessageBag');
            
            $validator = m::mock('Muratsplat\Multilang\Validator');
            
            $validator->shouldReceive('make')->andReturn(true);
            
            $this->multiLang =  new MultiLang(
                    new Picker(new Collection(),new Element()),
                    new Content(), 
                    $mockedConfig, 
                    $messageBag,
                    $validator);
            
        }

        public function testCheckMainImplement() {

            $this->assertTrue($this->multiLang->create(array(), new Content()));
            
            
        }    
    
    
}
