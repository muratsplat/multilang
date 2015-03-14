<?php namespace Muratsplat\Multilang\Events;

use Muratsplat\Multilang\MultiLang;

/**
 * Base Class includes common methods for all classes
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
abstract class EventBase {
    
    /**
     * @var \Muratsplat\Multilang\MultiLang
     */
    protected $multilang;
    
        /**
         * \Muratsplat\Multilang\MultiLang
         * 
         * @param MultiLang $multilang
         */
        public function __construct() {           
            
            // to getting Multilang Instance
            $this->multilang = \App::make('multilang');        }
        
        /**
         * Event Handle
         */
        abstract  public function handle($data); 
              
}
