<?php namespace Muratsplat\Multilang\Traits;

use Muratsplat\Multilang\Exceptions\LanguageException;
use Muratsplat\Multilang\Exceptions\MultiLangConfigNotCorrect as ConfigNot;

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
         * To check default language
         * 
         * @return boolean
         */
        public function isDefaultLanguage() {
            
            $language   = $this->getLanguage();
           
            $default    = $language->getDefaultLanguage();
            
            $langID     = $this->getLangIDColumnName();
            
            return $this->attributes[$langID] === (integer) $default->id;
        }        
        
        /**
         * to get language model
         * 
         * @return \Muratsplat\Multilang\Traits\modelName
         * @throws LanguageException
         */
        protected function getLanguage() {
            
            $modelName = \Config::get('multilang::languageModel');
            
            if (!class_exists($modelName, true)) {
                                
                throw new LanguageException("$modelName class is not exist or was not found!");                          
            }
            
            $model = new $modelName();
                        
            if (is_subclass_of($model, 'Illuminate\Database\Eloquent\Model', true) ) {
                
                return $model;                
            }
            
            throw new LanguageException('Language Model must be instance of Eloquent Model!');
        }
        
        /**
         * to get reserved attribute name to find language model
         * 
         * @return string attribute nane
         * @throws ConfigNot
         * 
         */
        protected function getLangIDColumnName() {
            
            $name  = \Config::get('multilang::reservedAttribute');
                        
            if ($this->offsetExists($name)) {
                
                return $name;                               
            }
            
            throw new ConfigNot("'reservedAttribute' can not be found on Lang Model!,"
                        . "Please check MultiLang configurations!");
        }
        
     
}
