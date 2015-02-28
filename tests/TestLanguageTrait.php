<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Tests\Model\Language;
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
class TestLanguageTrait  extends MigrateAndSeed {
    
    /**
     * @var Muratsplat\Multilang\Tests\Model\Language
     */
    private $language;   
    

        public function setUp() {
            parent::setUp();
                     
            $this->language = new Language();
            
        }
        
        public function testIsDefaultLanguage() {
            
            $def = $this->language->getDefaultLanguage();            
            
            $this->assertEquals($def, $this->language->query()->where('default', 1)->first());
            
        }
        
}
