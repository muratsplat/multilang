<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Element;
use Muratsplat\Multilang\Validator;
use Illuminate\Support\Collection;
use Muratsplat\Multilang\Tests\Model\Content;
use Muratsplat\Multilang\Tests\Model\ContentLang;
use \Mockery as m;
use PHPUnit_Framework_TestCase as UnitTest;

/**
 * a test class for \Muratsplat\Multilang\Validator
 *
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestValidator extends UnitTest {    
        
    /**
     *
     * @var Muratsplat\Multilang\Validator 
     */
    private $validator;
    
    /**multiLang
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
            
            $mockedConfig->shouldReceive('get')->with('multilang::prefix')->andReturn('@');
            
            $mockedConfig->shouldReceive('get')->with('multilang::appLanguageModel')->andReturn('Lang');
            
            $mockedConfig->shouldReceive('get')->with('multilang::reservedAttribute')->andReturn('__lang_id__');
             
            $mockedConfig->shouldReceive('make')->andReturn(true); 
            
            $messageBag = m::mock('Illuminate\Support\MessageBag');
            $laraValidator = m::mock('Illuminate\Validation\Factory');
            
            $validateObj = m::mock('stdClass');
            $validateObj->shouldReceive('fails')->andReturn('false');
            $validateObj->shouldReceive('getMessageBag')->andReturn($messageBag);
            
            $laraValidator->shouldReceive('make')->andReturn($validateObj);
            
            $this->picker = new Picker(new Collection(), new Element(), $mockedConfig);
            $this->picker->import($this->rawPost);
            
            $this->validator = new Validator($laraValidator, $mockedConfig);
            
        }        
        
        private function getMainModel() {
            
            return  new Content();
        }
        
        private function getLangModel() {
            
            return new ContentLang();
        }

        public function testSimleValidate() {
            
            $model = new Content();
            
            $this->assertFalse($this->validator->make($this->picker, $model, array())); 
        }
        
        public function testMergingRules() {
            
            $rulesShoudldBe = [
                
                'enable'    => 'required',
                'visible'   => 'required',
                
                'title@1'   => 'max:100',
                'content@1' => 'max:15000',
                'title@2'   => 'max:100|RequiredForDefaultLang:Page Title',
                'content@2' => 'max:15000',
                'title@3'   => 'max:100|RequiredForDefaultLang:Page Title',
                'content@3' => 'max:15000',
            ];
            
            $main = $this->getMainModel();            
                       
            $this->validator->make($this->picker, $main, array('title@1'   => 'max:100'));
            
            $this->assertEquals($rulesShoudldBe, $this->validator->getRules());
            
        }
        
        public function testIssueLastRulesStillExistWheItMakesNewValidation() {            
            
            $rulesShoudldBe = [

                'enable'    => 'required',
                'visible'   => 'required',

                'title@1'   => 'max:100',
                'content@1' => 'max:15000',
                'title@2'   => 'max:100|RequiredForDefaultLang:Page Title',
                'content@2' => 'max:15000',
                'title@3'   => 'max:100|RequiredForDefaultLang:Page Title',
                'content@3' => 'max:15000',
            ];
            
            $main = $this->getMainModel();
            
            $this->validator->make($this->picker, $main, array('oldRulesFromOldJobs'   => 'BlaAndBla'));
                       
            $this->validator->make($this->picker, $main, array('title@1'   => 'max:100'));            
            
            $this->assertEquals($rulesShoudldBe,$this->validator->getRules());           
        }
    
}
