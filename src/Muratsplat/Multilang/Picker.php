<?php namespace Muratsplat\Multilang;

use Illuminate\Support\Collection;
use Muratsplat\Multilang\Element;
use Muratsplat\Multilang\Exceptions\ElementUndefinedProperty;
use Muratsplat\Multilang\Exceptions\PickerUnknownError;
use Muratsplat\Multilang\Exceptions\PickerError;

/**
 * Picker Class
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
     * A prefix for realizing multi-language content
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
     * Picker element result will be 
     * recorded into this property
     *
     * @var array 
     */
    private $pickerResults = array();
    
    /**
     *
     * @var \Muratsplat\Multilang\Element 
     */
    private $element;

        /**
         * Connstructor
         * 
         * @param Collection $collection
         */
        public function __construct(Collection $collection, Element $element) {
           
            $this->collection = $collection;
            
            $this->element = $element;
            
        }        
   
        /**
         * To import raw post data
         *  
         * @param array $post
         * @return boolean
         */
        public function import(array $post=array(), $oldDelete = true) {
            
            if($oldDelete) {
                // delete old items
                $this->collection = $this->collection->filter(function($item) {
                    
                    return false;                    
                });
            }
            
            $this->rawPost = $post;
            
            return $this->startPicker();                      
        }        
        
        /**
         * To start to pick $post data up!
         * 
         * @return boolean
         * @throws Muratsplat\Multilang\Exceptions\PickerError
         */
        protected function startPicker() {
            
            try {
                
                $this->pickerMultiLangElemets($this->rawPost);
                
                $this->anyfails();
                
                $this->collection = $this->cleanCollection();
                
                return true;
                                
            } catch (ElementUndefinedProperty $e) {
                
                throw new PickerError($e->getMessage());
                
            } catch (PickerUnknownError $e) {
                
                throw new PickerError('Probably you have founded a bug..!');                           
            }       
        }
                        
        /*
         * Simple picker multi language elements
         * 
         */
        private function pickerMultiLangElemets(array $array=array()) {
                        
            foreach ($array as $k => $v) {   

                $pos = $this->isMultilang($k); 

                if(!is_bool($pos)) {
                                        
                    // deleting the prefix
                    $key = $this->removePrefixAndId($k, $pos);
                    
                    $id = $this->getLangId($k, $pos); //substr($k, $pos+1, strlen($k));
                    
                    $this->addResult($this->createOrUpdate($id, $key, $v, $multilang = true));
                    
                    continue;
                }
                // creating new non-multilang element
                $this->addResult($this->createOrUpdate($id=null,$k, $v, $multilang=false));
            }            
        }
        
        /**
         * to check that element is multilang 
         * or not by looking the prefix
         * 
         * @param string $key
         * @return boolean|int false, if it is non-multilang.
         */
        public function isMultilang($key) {
            
            return strpos($key, $this->defaultPrefix);
            
        }
        
        /**
         * to get lang id from $key by looking
         * prefix position . Lang id location is right side of the prefix
         * 
         * @param string $key element key
         * @param int $pos  prefix possition
         * @return int|null lang id number in key. null, if lang id doesn't
         * existed.
         */
        private function getLangId($key, $pos) {            
            
            $lang_id = substr($key, $pos+1, strlen($key));
            
            return is_string($lang_id) && !strlen(trim($lang_id)) ? null : (integer) $lang_id ;            
            
        }
        
        /**
         * To remove lang prefix and id 
         * and return key
         * 
         * @param string $name
         * @param integer $pos position of the number of prefix is will be deleted
         * @return string
         */
        public function removePrefixAndId($name, $pos) {

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
                
                 // trying to updating non-miltilang element..                
                $resultNon = $this->updateNonMultilang($v, $key, $value);
                
                if (!is_null($resultNon)) {                   
                    
                    $v = $resultNon;
                    
                    return true;
                }
                
                // Updating existed multilang element..
                $result = $this->updateMultilang($id, $v, $key, $value, $multilang);
                
                if (!is_null($result)) {                   
                                    
                    $v = $result;
                    
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
            
            $item = $this->element->newElement();
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
         * it only return multilang element 
         * 
         * @param int $lang_id
         * @return null\Muratsplat\Multilang\Element;
         */
        public function getById($lang_id) {
            
            foreach ($this->collection->all() as $v ) {
                
                if ($v->getId() === (integer) $lang_id) {
                   
                    return $v;                    
                }                
            }
            
            return null;           
        }
        
        /**
         * to get only non-multilang element
         * 
         * @return Illuminate\Support\Collection 
         */
        public function getNonMultilang() {
            
            $callback = function(Element $item) {
                
                return !$item->isMultilang();
                
            };
            
            return $this->collection->filter($callback);            
        }
        
        /**
         * to get only multilang element
         * 
         * @return Illuminate\Support\Collection 
         */
        public function getMultilang() {
            
            $callback = function(Element $item) {
                
                return $item->isMultilang();
                
            };
            
            return $this->collection->filter($callback);            
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
        
        /**
         * Updater for non-multilang elements
         * 
         * @param Element $item
         * @param string $key
         * @param mixed $value
         * @return null|Muratsplat\Multilang\Element  null, if it is not succesed
         */
        protected function updateNonMultilang(Element $item, $key, $value) {
            
             // updating non-miltilang element..
            if (!$item->isMultiLang() && $this->isKeyExist($item, $key)) {                   

                return $this->update($item, $key, $value, false);
            }
            
            return null;
        }
        
        /**
         * Updater for multilang elements
         * 
         * @param int $id
         * @param ElemMuratsplat\Multilang\Elementent $Createditem
         * @param string $key
         * @param mixed $value
         * @param boolean $multilang
         * @return null|Muratsplat\Multilang\Element  null, if it is not succesed
         */
        protected function updateMultilang($id, Element $Createditem, $key,$value, $multilang=true) {
            
            $item =$this->getById($id);
            
            if (is_null($item)) {
                
                return null;
            }
            
            if ($Createditem->isMultiLang() && $Createditem->isKeyExisted($key) && $this->isEmpty($value)) {               
                
                return $this->update($Createditem, $key, null, true);              
            }
            
            // Updating existed element..
            if ($Createditem->isMultilang() && $Createditem->getId() === (integer) $id) {

                return $this->update($Createditem, $key, $value, $multilang);
            }
            
            return null;            
        }
        
        /**
         * Same method resaults is may serious data 
         * to fix issues. If there is false returned, probably
         * a bug is in somewhere.
         * 
         * @param array $result
         */
        private function addResult($result) {
            
            array_push($this->pickerResults, $result);            
        }
        
        /**
         * Let's make sure everything is ok!
         * 
         * @return boolean true, if some process is failed!
         */
        protected function anyfails() {
            
            $callback = function($item) {
              
                if ($item === false) {
                    
                    return true;
                }
                
                return false;
            };            
            
            if (count(array_filter($this->pickerResults, $callback))) {
                
                throw new PickerUnknownError("Post data is not imported in succes!");
            }
        }
        
        /**
         * to get raw post which is imported in the object.
         * 
         * @return array
         */
        public function getSource() {
            
            return $this->rawPost;
        }
        
        /**
         * to convert non-multilang items to array without array's index.
         * 
         * @return array
         */
        public function getNonMultilangToArray() {
           
            $tmpArray= array();

            foreach ($this->getNonMultilang()->toArray() as $v) {

               $tmpArray = array_merge($tmpArray, $v);

            }
           
            return $tmpArray;   
        }
        
        /**
         * to convert multilang items to array with adding '__lang_id__' key
         * and the item's language id as value.
         * 
         * Example:  
         *      [
         *          ['__lang_id__' => 1, 'title' => 'Foo', 'content' => 'Baar'],
         *          ['__lang_id__' => 2, 'title' => 'FooBus', 'content' => 'Bobuus'],
         *          ...
         *      ]
         * 
         * @return array
         */
        public function getMultilangToArray() {
            
            $tmpArray=array();
            
            foreach ($this->getMultilang() as $v) {                
                
                $tmpArray[] = array_merge($v->toArray()[$v->getId()], ['__lang_id__' => $v->getId()]);                
            }
            
            return $tmpArray;   
        }
        
        /**
         * to check that post data is multilang.
         * If post data includes multi language elements,
         * returns true
         * 
         * @return boolean
         */
        public function isPostMultiLang() {
            
            return 0 !== count($this->getMultilang());            
            
        }
                

}
