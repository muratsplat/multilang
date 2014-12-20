<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Tests\Base;
use Muratsplat\Multilang\Element;
use Muratsplat\Multilang\MultiLang;
use Illuminate\Support\Collection;
use Illuminate\Config\Repository as Config;
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
    
        /**
         * For Mock
         */
        public function tearDown() {
            parent::tearDown();        
            m::close();
        }

        public function testFirstSimple() {

            $mockedConfig = m::mock('Illuminate\Config\Repository','Illuminate\Config\LoaderInterface');             

            $obj= new MultiLang(new Picker(new Collection(), new Element()), new Content,$mockedConfig);
        }
        
        public function setUp() {
            parent::setUp();
            
            $mockedConfig = m::mock('Illuminate\Config\Repository','Illuminate\Config\LoaderInterface');
            
            $this->multiLang =  new MultiLang(new Picker(new Collection(), new Element()), new Content,$mockedConfig);
            
        }

        public function testCheckMainImplement() {

            $this->multiLang->create(array(), new Content());
            
        }

    
    
    
    
    
    
    
}