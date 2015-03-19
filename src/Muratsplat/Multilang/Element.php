<?php namespace Muratsplat\Multilang;

use Muratsplat\Multilang\Exceptions\ElementUndefinedProperty;
use Illuminate\Support\Contracts\ArrayableInterface;

/**
 * Simple Picker Class
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class Element implements ArrayableInterface {

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
     * Elements are reserved by Laravel
     *
     * @var array 
     */
    private $ignoredElements = array('_token');
    
    
        /**
         * Simple Constructer
         * 
         * @param array $attibutes
         */
        public function __construct(array $attibutes = array()) {
            
            $this->data = $attibutes;
        }
           
        /**
         * Set Method for overloading
         * 
         * @param string $name
         * @param mixed $value
         * @return void
         */
        public function __set($name, $value) {           
            
            switch (true) {
                
                case $this->isIgnored($name) : return;
                
                case $this->isEmpty($value) : unset($this->data[$name]); return;
                                
                default :  $this->data[$name] = $value;                    
            }        
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
            
        }
        
        /**
         * getter for id property
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
         * To set for multilang property
         * 
         * You can type element which is multilang or non-multilang
         * 
         * @param  $isMultilang
         */
        public function setMultilang($isMultilang) {
            
            $this->multilang = (boolean) $isMultilang;
        }
        
        /**
         * to check overloaded property
         * 
         * @param string $key
         * @return boolean
         */
        public function isKeyExisted($key) {
            
            if (is_null($key)) {
                
                return false;
            }
            
            return array_key_exists($key, $this->data);
        }
        
        /**
         * if all property is null,
         * return true, not false
         *
         * 
         * @return boolean
         */
        public function allkeyNull() {            
            
            return  0 === count(array_filter($this->data, function($property){                
                
                if (!$this->isMultiLang()) {
                    
                    return true;
                }
                
                if(is_string($property)) {
                    
                    return true;
                }
                
                return !is_null($property);
                
            }));           
        }
        
        /**
         * To get new Object
         * 
         * @return \Muratsplat\Multilang\Element
         */
        public function newElement(array $attribute = array()) {
             
            return new static($attribute);
        }
        
        /**
          * Get the instance as an array.
          *
          * @return array
          */
        public function toArray() {
            
            if(!$this->multilang) {
                
                return $this->data;
            }
            
            return $this->multilangArray();           
        }
        
        /**
          * If object is multilang element, 
          * get the instance as an array.
          *
          * @return array
          */
        private function multilangArray() {
                        
            return array($this->lang_id => $this->data);
        }
        
        /**
         * to check what key is ignored..
         * 
         * @param string  $key element name
         * @return bool
         */
        private function isIgnored($key) {
            
            return in_array($key, $this->ignoredElements);
        }

}
