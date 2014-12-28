<?php namespace Muratsplat\Multilang\Traits;

use Muratsplat\Multilang\Exceptions\RelationNotCorrect;

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
            // we have to sure everything is ok!!
            $this->checkRelation();
        
            return $this->langModels()->getResults()->count() >= 1;
        }        
                
        /**
         * to make sure what correct relations between main model and lang models
         * 
         * @throws \Muratsplat\Multilang\Exceptions\RelationNotCorrect
         */
        protected function checkRelation() {
                    
            $nameLang = get_class($this);
            
            if (!$this->langModels()->getRelated()->mainModel()->getRelated() instanceof $nameLang) {
                
                throw new RelationNotCorrect("It looks the relation is not correct "
                        . "between main model and multi-language models");                
            }          
        }
   
}
