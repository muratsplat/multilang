<?php namespace Muratsplat\Multilang;

use Muratsplat\Multilang\Exceptions\ElementUndefinedProperty;
use Muratsplat\Multilang\Exceptions\ElementPropertyAlreadyDefined;
/**
 * Simple Picker Class
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */
class Element {

    /**
     * If item is multi language,
     * it will be true
     *
     * @var boolean 
     */
    private $multilang = false;
  
    /**
     * language id on database
     * 
     * @var integer  
     */
    private $lang_id = 0;  
    
    /**
     * For overloaded data
     * @var array 
     */
    private $data = array();
      
        
        /**
         * Set Method for overloading
         * 
         * @param string $name
         * @param mixed $value
         * @return void
         */
        public function __set($name, $value) {
            
            if (array_key_exists($name, $this->data)) {
                                             
               if ($this->isEmpty($value)) {
                   
                   unset($this->data[$name]);
                   
                   return;
               }
                                
            }
            
            $this->data[$name] = $value;
        }        
        
        /**
         * When object is overloading, checking 
         * the property value as for empty or not
         *
         * @param mixes $value it can be string or int
         * @return boolean
         */
        private function isEmpty($value) {
            
            if(!$this->isMultiLang()) {
                
                /*
                 *  if the post element is non-multilang,
                 *  It doesn't metter for us.
                 */                
                return false;
            }
            
                       
            if (is_string($value) && strlen(trim($value)) === 0) {
                                 
                return true;
            }
            
            return false;
        }


        /**
         * Getter method for overloading
         * 
         * @param type $name
         * @return mixed
         * @throws \Muratsplat\Multilang\Exceptions\ElementUndefinedProperty
         */
        public function __get($name) {
            
            if (!array_key_exists($name, $this->data)) {
                
                throw new ElementUndefinedProperty("[$name] property is undefined!");
                                
            }
          
            return $this->data[$name];
        }

        /**
         * Isset method for checking overloading properties
         *
         * @param string
         * @return boolean
         */
        public function __isset($name) {
                        
            return in_array($name, $this->data);
        }
        
        /**
         * Unset method for overloading properties
         * 
         * @param string $name
         * @return void
         * @throws \Muratsplat\Multilang\Exceptions\ElementUndefinedProperty
         */
        public function __unset($name) {
            
            if (!$this->$name) {
                
                throw new ElementUndefinedProperty("[$name] property is undefined!");
            }
            
            unset($this->data[$name]);
            
            return;
        }
        
        /**
         * getter  for id property
         * 
         * @return int
         */
        public function getId() {
            
            return (integer) $this->lang_id;
        }
        
        /**
         * getter for multilang
         * 
         * @return boolean
         */
        public function isMultiLang() {
            
            return $this->multilang;
        }
        
        /**
         * setter for lan_id property
         * 
         * @param int $id
         */
        public function setId($id) {
            
            $this->lang_id = (integer) $id;
        }
        
        /**
         * setter for multilang property
         * 
         * @param  $isMultilang
         */
        public function setMultilang($isMultilang) {
            
            $this->multilang = (boolean) $isMultilang;
        }

}