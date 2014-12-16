<?php namespace Muratsplat\Multilang;

use Illuminate\Support\Collection;
use Muratsplat\Multilang\Exceptions\PickerUndefinedProperty;
use Muratsplat\Multilang\Exceptions\PickerOnlyArray;

/**
 * Simple Picker Class
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */
class Picker {
 
    /**
     * Collection Class 
     * 
     * @var \Illuminate\Support\Collection
     */
    protected $collection;   
    
    /**
     * For overloaded data
     * @var array 
     */
    private $data = array();
    
    /**
     * Firstly post data stored in there.
     * 
     * @var array 
     */
    private $rawPost = array();
    
    /**
     * A prefix for realizing multi language content
     * 
     * @var string 
     */
    private $defaultPrefix = '@';    
    

        /**
         * Connstructor
         * 
         * @param Collection $collection
         */
        public function __construct(Collection $collection) {
           
            $this->collection = $collection;      
            
        }        
        
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
         * @return mixes|null 
         */
        public function __get($name) {
            
            return in_array($name, $this->data) ? $this->data[$name] :  null;
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
         */
        public function __unset($name) {
            
            if (!$this->$name) {
                
                throw new PickerUndefinedProperty("[$name] property is undefined!");
            }
            
            unset($this->data[$name]);
            
            return;
        }
        
        /**
         * To import raw post data
         * 
         * @param array $post
         */
        public function import(array $post=array()) {
            
            $this->rawPost = $post;
            
            
                       
        }
        
        private function convertToObject() {
            
            
        }
        
        
        
        
        
        
        
 
 
 }
