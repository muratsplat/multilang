<?php namespace Muratsplat\Multilang\Tests;

use Muratsplat\Multilang\Tests\Model\Content;
use Muratsplat\Multilang\Tests\Model\ContentSoftDelete;
use \Mockery as m;

/**
 *  Common methods for all test class to not repeat it
 *
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
trait CreateContentAndLangTraitForTest {    
    
        public function createContent($n) {

            for ($i=0; $i< $n ; $i++) {

                Content::create(['enable' => '1', 'visible' => 2])->save();
            }
            
            return Content::all()->count() === $n;        
        }        

        public function createContentLang($n) {

            $callback = function($item) use ($n) {

                for ($i=0; $i< $n ; $i++) {

                    $item->ContentLangs()->create([

                    '__lang_id__'   => $i, 
                    'title'         => str_random(5),
                    'content'       => str_random(10)
                    ])->save();                   
                }               
            };

            Content::all()->each($callback);

            return Content::find(1)->ContentLangs()->getResults()->count() === $n;
        }
        
        public function createContentSoftDelete($n) {

            for ($i=0; $i< $n ; $i++) {

                ContentSoftDelete::create(['enable' => '1', 'visible' => 2])->save();
            }
            
            return Content::all()->count() === $n;        
        }        

        public function createContentLangSoftDelete($n) {

            $callback = function($item) use ($n) {

                for ($i=0; $i< $n ; $i++) {

                    $item->ContentLangs()->create([

                    '__lang_id__'   => $i, 
                    'title'         => str_random(5),
                    'content'       => str_random(10)
                    ])->save();                   
                }               
            };

            ContentSoftDelete::all()->each($callback);

            return ContentSoftDelete::find(1)->ContentLangs()->getResults()->count() === $n;
        }
        
                
        /**
         * 
         * @return \Mockery\MockInterface
         */
        protected function getMockedConfig() {
            
            return m::mock('Illuminate\Config\Repository');
                           
        }
        
        /**
         * 
         * @return \Mockery\MockInterface
         */
        protected function getMockedMessageBag() {
            
            return m::mock('Illuminate\Support\MessageBag');
        }
        
        /**
         * 
         * @return \Mockery\MockInterface
         */
        protected function getMockedValid() {
            
            return m::mock('Muratsplat\Multilang\Validator');
        }
        
        /**
         * 
         * @return \Mockery\MockInterface
         */
        protected function getWrapper() {
            
            return m::mock('Muratsplat\Multilang\Wrapper');
        }
}
