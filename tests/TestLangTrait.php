<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Tests\Model\ContentLang;
// for testing CRUD ORM jobs..
use Muratsplat\Multilang\Tests\MigrateAndSeed;
use \Mockery as m;

/**
 * a test class for \Muratsplat\Multilang\Wrapper
 *
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class TestLangTrait  extends MigrateAndSeed {
    
    /**
     * @var \Muratsplat\Multilang\Tests\Model\ContentLang
     */
    private $contentLang;   
  
    /**
     * Laravel Config Object
     * 
     * @var type 
     */
    private $config;
    

        public function setUp() {
            parent::setUp();
                     
            $this->config = m::mock('Illuminate\Config\Repository');
            
            $this->config->shouldReceive('get')->with('mutilang.languageModel')->andReturn('Muratsplat\Multilang\Tests\Model\Language');
            
            $this->config->shouldReceive('get')->with('multilang.reservedAttribute')->andReturn('__lang_id__');
            
            $this->config->shouldReceive('offsetGet')->andReturn('testbench');
            
            $this->app['config'] = $this->config;
            
            $this->contentLang = new ContentLang();
            
        }
        
        public function testIsDefaultLanguageExpectTrue() {
            
            $attributes = [
                'title'         => 'Foo', 
                'content'       => 'Foo Content',
                '__lang_id__'   => 1,
                'content_id'    => 1,
                ];
            $new  = $this->contentLang->create($attributes);         
                       
            $this->assertTrue($new->isDefaultLanguage());
            
        }
        
        public function testIsDefaultLanguageExpectFalse() {
            
            $attributes = [
                'title'         => 'Foo', 
                'content'       => 'Foo Content',
                '__lang_id__'   => 2, // it is not default
                'content_id'    => 1,
                ];
            $new  = $this->contentLang->create($attributes);         
                       
            $this->assertFalse($new->isDefaultLanguage());
            
        }
        
}
