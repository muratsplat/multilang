<?php namespace Muratsplat\Multilang\Traits;


/**
 * The Trait  for Multi Language Models
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
trait LangTrait  {    
       
        /**
         * to get Rules for validation
         * 
         * @return array
         */
        public function getRules() {
        
            return $this->rules;
        }
        
        /**
         * To check attributes to be ready.
         * We dont want empty value on the view layer 
         * 
         * @return boolean
         */
        public function isReady() {

            return 0 === count(array_filter($this->getRequiredAttributes(), function($item) {
                
                if($item === null || $item === '' || empty($item)) {
                    
                    return true;
                }        
                
            }));
            
        }
        
        /**
         * to required attributes for multilang
         * 
         * @return array
         */
        public function getRequiredAttributes() {
            
            return $this->requiredAttributes;
        }
      
}
