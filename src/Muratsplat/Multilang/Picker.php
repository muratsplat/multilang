<?php namespace Muratsplat\Multilang;

use Illuminate\Support\Collection;
use Muratsplat\Multilang\Element;
use Muratsplat\Multilang\Exceptions\ElementUndefinedProperty;

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
     * Firstly post data stored in there.
     * 
     * @var array 
     */
    private $rawPost = array();
    
    /**
     * A prefix for realizing multi language content
     * 
     * Example:
     * 
     * array(foo@1=> value,foo@2=> value);
     * 
     * In this examle, '@' charecters is a prefix and
     * the number which is the right side of it must be language id. 
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
         * To import raw post data
         * 
         * @param array $post
         * @return boolean
         */
        public function import(array $post=array()) {
            
            $this->rawPost = $post;
            
            try {
                
                $this->pickerMultiLangElemets($post);
                
                $this->collection = $this->cleanCollection();
                
                return true;
                                
            } catch (ElementUndefinedProperty $e) {
               
                return false;
                
            }                    
        }
                
        /*
         * Simple picker multi language elements
         * 
         */
        private function pickerMultiLangElemets(array $array=array()) {
            
            foreach ($array as $k => $v) {   

                $pos =strpos($k, $this->defaultPrefix); 

                if($pos !== false) {
                                        
                    // deleting the prefix
                    $key = $this->removePrefixAndId($k, $pos);
                    
                    $id = substr($k, $pos+1, strlen($k));
                    
                    $this->createOrUpdate($id, $key, $v, $multilang = true);
                    
                    continue;
                }
                
               $this->createOrUpdate($id=null,$k, $v, $multilang=false);
            }            
            
        }
        
        /**
         * To remove lang prefix and id 
         * and return key
         * 
         * @param string $name
         * @param integer $pos position of the number of prefix is will be deleted
         * @return string
         */
        private function removePrefixAndId($name, $pos) {

            return substr($name, 0, $pos);
        }
        
        
        /**
         *  Create or update method
         * 
         * If element is already created, it will be updated,
         * or not creating new one
         * 
         * @param type $id
         * @param type $key
         * @param type $value
         * @param type $multilang
         * @return boolean
         */
        protected function createOrUpdate($id, $key, $value, $multilang=false) {
            
            foreach ($this->collection->all() as $v) {
                
                 $item = $this->getById($id);
                
                // İf before created mutlilang element is updated by empty value
                // to mark let's make null to it
                if (!is_null($item) && $item->isMultiLang()
                        && 
                        $item->isKeyExisted($key) && $this->isEmpty($value)) {
                    
                    $v = $this->update($item, $key, null, $item->isMultiLang());
                       
                    return true;  
                    
                }         
                
                // Updating existed element..
                if ($v->isMultilang() === true && $v->getId() === (integer) $id) {
                    
                    $v = $this->update($v, $key, $value, $multilang);
                    
                    return true;
                }
                
                // updating non-miltilang element..
                if (is_null($id) && !$v->isMultiLang() && $this->isKeyExist($v, $key)) {                   
                
                   $v = $this->update($v, $key, $value, $multilang);
                    
                    return true;
                }
            }            
            
            return $this->create($id, $key, $value, $multilang);           
        }
                
        /**
         * to create new multi-language content
         * 
         * @param int $id
         * @param string $key
         * @param mixed $value
         * @param boolean $multilang
         * @return boolean
         */
        protected function create($id,$key, $value, $multilang = false) {
            
            $item = new Element();
            // for non-multilang elements it is not need to set null,
            $item->$key = $this->valueSelecter($value, $multilang);                    
            
            $item->setId($id);
            
            $item->setMultilang($multilang);            
            
            $this->collection->push($item);
            
            return $this->collection->last() === $item;
        }
        
        /**
         * to update element
         * 
         * @param Element $item
         * @param type $key
         * @param type $value
         * @param type $multilang
         * @return Muratsplat\Multilang\Element
         */
        protected function update(Element $item, $key, $value, $multilang = null) {
                       
            if (is_null($multilang) || $multilang === false) {               
                
                $item->$key=$value;
                
                return $item;
               
            }
            
            $item->setMultilang($multilang);
            
            $item->$key = $this->valueSelecter($value, $multilang);
            
            return $item;
                       
        }
        
        /**
         * to check the property of the element.
         * it is supported to non-multilang and multilang
         * elements 
         * 
         * @param Muratsplat\Multilang\Element $v
         * @param string $key
         * @return boolean
         */
        private function isKeyExist($v, $key) {            
            
            try {
                
                if($v->getId() === 0 && !$v->isMultilang()) {
                
                    $v->$key;
                
                    return true;
                }                
                
                $v->$key;
                
                return true;
                
            } catch (ElementUndefinedProperty $ex) {
                
                return false;

            }   
        }
        
        /**
         * to check that inputed value is 
         * empty
         * 
         * @param mixed $value
         * @return boolean
         */
        public function isEmpty($value) {
            
            if(is_string($value) && !strlen(trim($value))) {
                
                return true;
            }
            
            if(is_array($value) && !count($value)) {
                
                return true;
            }
            
            return false;
        }
        
        /**
         * get element by id
         * 
         * @param int $lang_id
         * @return \Muratsplat\Multilang\Element;
         */
        public function getById($lang_id) {
            
            foreach ($this->collection->all() as $v ) {
                
                if ($v->getId() === (integer) $lang_id) {
                   
                    return $v;
                    
                }                
            }
            
            return null;           
        }
        
        public function getNonMultilang() {
            
            
        }
        
        /**
         * Get the collection of elements as a plain array.
         * 
         * @return array
         */
        public function toArray() {
            
            return $this->collection->toArray();
        }
        
        /**
         * Get the collection of elements as plain array
         * 
         * @return Illuminate\Support\Collection
         */
        public function getCollection() {
            
            return $this->collection;
        }
        
        /**
         * Only return items are which one has 
         * all overloaded property(key) not be null   
         *
         * @return Illuminate\Support\Collection
         */
        protected function cleanCollection() {
            
            $callback = function(Element $item) {
                
                
                return !$item->allkeyNull();
                            
            };
            
            return $this->collection->filter($callback);      
        }
        
        /**
         * An helper for setting value.
         * while multilang element's property is setting,
         * if the value is empty, this method will mark by return null.
         * 
         * for non-multilang element it doesn't metter 
         *  
         * @param mixed $value
         * @param boolean $multilang
         * @return mixed
         */
        private function valueSelecter($value, $multilang=false) {
            
            
            return $multilang && $this->isEmpty($value) ? null: $value;                    
        }
      
        
        
}
