<?php namespace Muratsplat\Multilang;

use Muratsplat\Multilang\Exceptions\ElementUndefinedProperty;
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
            
            $this->data[$name] = $value;
        }        
        
        /**
         * Getter method for overloading
         * 
         * @param type $name
         * @return mixed
         * @throws \Muratsplat\Multilang\Exceptions\ElementUndefinedProperty
         */
        public function __get($name) {
            
            if (!in_array($name, $this->data)) {
                
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

}