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
        
        
        protected function createOrUpdate($id, $key, $value, $multilang=false) {
            
            foreach ($this->collection->all() as $v) {
                
                if ($v->isMultilang() === true && $v->getId() === (integer) $id) {
                    
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
            
            $item->$key = $value;
            
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
            
            $item->$key=$value;
            
            if (!is_null($multilang)) {
                
                $item->setMultilang($multilang);                  
                
            }            
            
            return $item;
                       
        }
        
        /**
         * to check that inputed key is existed
         * in the collection object except that
         * non-multilang content
         * 
         * @param string $key
         * @param boolean $multilang
         * @return boolean
         */
        private function isKeyExisted($key, $multilang = false) {
           
            $existed = false;
                       
            foreach ($this->collection->all() as $v) {
                
                if ($multilang && $v->isMultilang()) {
                    
                    continue;                  
                    
                }
                
                try {
                    
                    $v->$key;
                    
                    $existed = true;                    
                    
                } catch (ElementUndefinedProperty $exc) {
                    
                    continue;
                }
                            
            }
            
            return $existed;           
        }

        
        
        public function getById($lang_id) {
            
            
        }
        
        public function getNonMultilang() {
            
            
        }
        
        public function toArray() {
            
            return $this->collection->toArray();
        }
        
        
        public function getCollection() {
            
            return $this->collection;
        }
        
        
}
