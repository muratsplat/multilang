<?php namespace Muratsplat\Multilang\Traits;



/**
 * The Trait for main models
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
trait MainTrait  {
    
        /**
         * to get Rules for validation
         * 
         * @return array
         */
        public function getRules() {
        
            return $this->rules;
        }
        
        /**
         * If the model has many language model,
         * returns true, not false.
         * 
         * @return bool
         */
        public function isMultilang() {
        
            return $this->langModels()->getResults()->count() >= 1;
        }      
}
