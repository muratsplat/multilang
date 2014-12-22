<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Tests\Base;
use Muratsplat\Multilang\Element;
//use Muratsplat\Multilang\MultiLang;
use Muratsplat\Multilang\Validator;

use Illuminate\Validation\Factory;
use Illuminate\Support\Collection;
//use Illuminate\Config\Repository as Config;
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
    
    /**
     * Picker Object
     *
     * @var Muratsplat\Multilang\Picker 
     */
    private $picker;
    
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
   
        
        public function tearDown() {
        
            parent::tearDown();        
            
            m::close();
        }
        
        public function setUp() {
            parent::setUp();
            
            $mockedConfig = m::mock('Illuminate\Config\Repository');
            
             $mockedConfig->shouldReceive('get')->with('prefix')->andReturn('@');
            
             $mockedConfig->shouldReceive('get')->with('appLanguageModel')->andReturn('Lang');

            
            $messageBag = m::mock('Illuminate\Support\MessageBag');
            $laraValidator = m::mock('Illuminate\Validation\Factory');
                        
            $this->picker = new Picker(new Collection(), new Element());
            
            $this->picker->import($this->rawPost);
            
            $this->validator = new Validator($messageBag, $laraValidator, $mockedConfig); 
            
            

        }

        public function testSimleValidate() {
            
            $model = new Content();
            
            $this->validator->make($this->picker, $model, array());
            
                       

          
        }

    
    
    
    
    
    
    
}