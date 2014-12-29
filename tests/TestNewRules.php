<?php namespace Muratsplat\Multilang\Tests;

use \Mockery as m;
use PHPUnit_Framework_TestCase as UnitTest;

/**
 * a test class for \Muratsplat\Multilang\NewRules
 *
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestNewRules extends UnitTest {    
        
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
        }
        
        public function testFistSimple() {
            
            $translator = m::mock('Symfony\Component\Translation\TranslatorInterface');
            
            new \Muratsplat\Multilang\NewRules($translator,array(), array());
        }
        
        public function testRequiredForDefaultLang() {
            
            $rules = ['title@1' => 'RequiredForDefaultLang:@,1'];
            
            $post = ['title@1' => ''];
            
            $translator  = $this->getTranslator();
            
            $translator->shouldReceive('trans')->andReturn('');            
            
            $v = new \Muratsplat\Multilang\NewRules($translator, $post, $rules);
            
            $this->assertTrue($v->fails());          
        }
        
        public function testRequiredForDefaultLangAssertedTrue() {
            
            $rules = ['title@1' => 'RequiredForDefaultLang:@,1'];
            
            $post = ['title@1' => 'Test'];
            
            $translator  = $this->getTranslator();
            
            $translator->shouldReceive('trans')->andReturn('');            
            
            $v = new \Muratsplat\Multilang\NewRules($translator,$post, $rules);
            
            $this->assertTrue($v->passes());
        }
        
        /**
         * 
         * @link https://github.com/laravel/framework/blob/4.2/tests/Validation/ValidationValidatorTest.php source
         */
        protected function getTranslator() {
            
            return m::mock('Symfony\Component\Translation\TranslatorInterface');
        }
         /**
         * 
         * @link https://github.com/laravel/framework/blob/4.2/tests/Validation/ValidationValidatorTest.php source
         */
        protected function getRealTranslator() {
            
            $trans = new Symfony\Component\Translation\Translator('en', new Symfony\Component\Translation\MessageSelector);
            $trans->addLoader('array', new Symfony\Component\Translation\Loader\ArrayLoader);
            return $trans;
        }    
}
