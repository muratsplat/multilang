<?php namespace Muratsplat\Multilang\Events;

use Muratsplat\Multilang\Events\EventBase;

/**
 * Base Class includes common methods for all classes
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class WantedLangEvent extends EventBase {    
    
        /**
         * @param type $data
         */
        public function handle($data) {
                   
            $this->multilang->getWrapperInstance()->setWantedLang($data);
        }
              
}
