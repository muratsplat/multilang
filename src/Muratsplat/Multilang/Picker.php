<?php namespace Muratsplat\Multilang;

use Illuminate\Support\Collection;
use Illuminate\Config\Repository as Config;

use Muratsplat\Multilang\Element;
use Muratsplat\Multilang\Exceptions\PickerUnknownError;
use Muratsplat\Multilang\Exceptions\PickerError;
use Muratsplat\Multilang\Base;

/**
 * Picker Class
 * 
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class Picker extends Base {
 
    /**
     * Collection Object 
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
     * Picker element result will be 
     * recorded into this property
     *
     * @var array 
     */
    private $pickerResults = array();
    
    /**
     * Element Object for elements in post data
     * 
     * @var \Muratsplat\Multilang\Element 
     */
    private $element;
    
    /**
     * Laravel Config Object 
     * 
     * @var \Illuminate\Config\Repository 
     */
    protected $config;    
    
    /**
     * Prefix to detect lang id
     *
     * @var string
     */
    private $prefix;
    
    /**
     * Reserved Column Name
     *
     * @var string
     */
    private $reservedColumnName;
    
        /**
         * Connstructor
         * 
         * @param Collection $collection
         */
        public function __construct(Collection $collection, Element $element, Config $config) {
           
            $this->collection   = $collection;
            
            $this->element      = $element;
            
            $this->config       = $config; 
            
            $this->prefix       = $this->getConfig('prefix');
            
            $this->reservedColumnName = $this->getConfig('reservedAttribute');
        }        
   
        /**
         * To import raw post data
         * 
         * @param array $post
         * @return \Muratsplat\Multilang\Picker
         */
        public function import(array $post=array()) {    
            
            $this->setRawPost($post);
            
            $this->reset();                     
                
            $this->scan();
                 
            $this->mergedSameElements();
                 
            $this->checkErrors();
            
            return $this;
        }        
        
        /**
         * to scan raw post data and convert Element Objects
         * 
         * @return \Illuminate\Support\Collection
         */
        protected function scan() {
            
            $post       = $this->getRawPost();               
             
            $callback   = function($key, $val) {
                
                if ($this->isEmpty($val)) { return; }            
                
                return $this->convertToElement($key, $val);           
            };
            
            $elements   = array_map($callback, array_keys($post), array_values($post));
            
            var_dump($elements);
                     
            $this->addItemToCollection($elements);            
        }
        
        /**
         * To convert post item to Element Object
         * 
         * @param type $key
         * @param type $val
         * @return type
         */
        protected function convertToElement($key, $val) {
            
             if ($this->isMultilang($key)) {
                 
                 return $this->contertToMultilangElement($key, $val);                 
             }
             
             if (!$this->isMultilang($key)) {
                 
                 return $this->contertToNonMultilangElement($key, $val);                
             }                         
            
        }        
        
        /**
         * To convert non-multilang post item to  Element Object 
         * 
         * @param string $key
         * @param mixed $val
         * @return \Muratsplat\Multilang\Element 
         */
        private function contertToNonMultilangElement($key, $val) {
            
            $element = $this->element->newElement();
            
            $element->{$key} =  $val;
            
            $element->setMultilang(false);
            
            return $element;
        }
        
        /**
         * To convert multilang post item to Element Object
         * 
         * @param string $key
         * @param mixed $val
         * @return \Muratsplat\Multilang\Element
         * @throws PickerError
         */ 
        private function contertToMultilangElement($key, $val) {
            
            $lang_ID = $this->parserLangId($key);
            
            if (is_null($lang_ID)) {
                
                throw new PickerError('Language ID was not taken. Therefore '
                        . 'can not created multilang element without the ID!');
            }
            
            $element = $this->element->newElement();
            
            $cleanKey = $this->removePrefixAndId($key);
            
            $element->{$cleanKey} = $val;
            
            $element->setMultilang(true);           
            
            $element->setId($lang_ID);
            
            return $element;                                               
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
        private function parserLangId($key) {
            
            $prefixPosition = strpos($key, $this->prefix);
            
            $lang_id = substr($key, $prefixPosition +1, strlen($key));
            
            return is_string($lang_id) && !strlen(trim($lang_id)) 
                    
                    ? null 
                    
                    : (integer) $lang_id;          
        }
             
        /**
         * to check that element is multilang 
         * or not by looking the prefix
         * 
         * @param string $key
         * @return bool.
         */
        protected function isMultilang($key) {
            
            $result = $this->findPrefixPosition($key);            
           
            return is_int($result) && !is_bool($result) ? true : false;
        }        
        
        /**
         * To get lannguage prefix at given key
         * 
         * @param string $key
         * @return int
         */
        private function findPrefixPosition($key) {
                             
            return strpos($key, $this->getConfig('prefix'));           
        }
              
        /**
         * To remove lang prefix and id 
         * and return key
         * 
         * @param string $name
         * @return string
         */
        public function removePrefixAndId($name) {
            
            $pos = $this->findPrefixPosition($name);
            
             if (is_bool($pos)) {
                
                throw new PickerError('Multilang prefix can not found!');
            }    
            
            return substr($name, 0, $pos);            
        }       
        
        /**
         * to check that inputed value is 
         * empty
         * 
         * @param mixed $value
         * @return boolean
         */
        public function isEmpty($value) {
            
            if (is_string($value) && !strlen(trim($value))) {
                
                return true;
            }
            
            if (is_array($value) && !count($value)) {
                
                return true;
            }
            
            return false;
        }
        
        /**
         * get element by id
         * it only return multilang element 
         * 
         * @param int $lang_id
         * @param bool
         * @return \Illuminate\Support\Collection|\Muratsplat\Multilang\Element;
         */
        public function getById($lang_id, $first = true) {
            
            $filtered = $this->collection->filter(function(Element $item) use ($lang_id){
                
                if($item->getId() === $lang_id) {
                    
                    return true;
                }     
            });
            
            return $first ? $filtered->first(): $filtered;       
        }
        
        /**
         * to get only non-multilang element
         * 
         * @return Illuminate\Support\Collection 
         */
        public function getNonMultilang() {
                        
            return $this->collection->filter( function(Element $item) {
                
                return !$item->isMultilang();
                
            });            
        }
        
        /**
         * to get only multilang element
         * 
         * @return Illuminate\Support\Collection 
         */
        public function getMultilang() {
                        
            return $this->collection->filter(function(Element $item) {
                
                return $item->isMultilang();                
            });            
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
            
            $reservedName = $this->reservedColumnName;
                       
            foreach ($this->getMultilang() as $v) {                
                
                $tmpArray[] = array_merge($v->toArray()[$v->getId()], [$reservedName => $v->getId()]);                
            }
            
            return $tmpArray;   
        }
        
        /**
         * To set raw post data
         * 
         * @param array $post
         */
        protected function setRawPost(array $post = array()) {
            
            $this->rawPost = $post;
        }        
            
        /**
         * To get raw post data
         * 
         * @return array
         */
        protected function getRawPost() {
            
            return $this->rawPost;
        }
        
        /**
         * To add item or items(in array) to object's collection
         * 
         * @param mixed $items
         * @return void
         */
        private function addItemToCollection($items) {
            
            if (!is_array($items)) {
                
                $this->collection->push($items);
                               
                return;
            }
            
            foreach ($items as $v) {
                
                $this->collection->push($v);                    
            }              
        }
        
        
        /**
         * To merged all same elements
         * 
         * This methods will merged all same elements and store in 
         * this object's collection
         *  
         * @return void
         */
        public function mergedSameElements() {
        
            $collection = $this->collection->make(array());
            /*
             * Creating multilang elements with merged attributes
             */
            foreach ($this->getMergedMultilangElementsToArray() as $lang_id => $attributes) {
                
                $newElem= $this->element->newElement($attributes);
                
                $newElem->setId($lang_id);
                
                $newElem->setMultilang(true);
                
                $collection->push($newElem);          
            }
            
            /**
             * Creating non-multilang element
             */
            $newElem= $this->element->newElement($this->getNonMultilangToArray());

            $newElem->setMultilang(false);

            $collection->push($newElem);           
            
            $this->collection = $collection;          
        }
        
        
        /**
         * to get merged attributes of multilang elements
         * 
         * @return array
         */
        private function getMergedMultilangElementsToArray() {            
            
             $elements      = $this->getMultilangToArray();
             
             $elementsById  = [];
             
             $lang_id_key   = $this->reservedColumnName;           
             
             foreach ($elements as $v) {                
               
                /*             [language id   =>      [   key      =>      value]] */                      
                $elementsById[$v[$lang_id_key]][array_keys($v)[0]] = $v[array_keys($v)[0]];
             }
             
             return $elementsById;
        }
        
        /**
         * To reset object's collection
         * 
         * @return void
         */
        protected function reset() {
            
            $this->collection = $this->collection->make(array());
        }
        
        /**
         * to get possible the number of elements by checking
         * raw post data.
         * 
         * This method can help to find bugs 
         * 
         * @return int
         */
        private function getPossibleNumberOfElements() {
            
            $multilang = 0;
            
            $non_mulltilang = 0;
            
            foreach ($this->rawPost as $key => $value) {
            
                if($this->isMultilang($key)) {
                    
                    $multilang++;
                    
                    return;
                }
                
                $non_mulltilang++;
                
            }
            
            return $multilang + $non_mulltilang;
        }
        
        /**
         * to get number of element objects
         * 
         * @return int
         */
        protected function getNumberOfElements() {
            
            return $this->collection->count();
        }
        
        /**
         * to check errors 
         * 
         * @throws \Muratsplat\Multilang\Exceptions\PickerUnknownError
         */
        protected function checkErrors() {
            
            $same = $this->getPossibleNumberOfElements() === $this->getNumberOfElements();
            
            if($same) {
                
                throw new PickerUnknownError('The number of created elements is not '
                        . 'equals to the number of possible elements!');
            }
            
        }
        
        /**
         * To get only multilang elements
         * 
         * @return \Illuminate\Support\Collection
         */
        public function getMultilangElements() {
            
            return $this->collection->filter(function(Element $item){
                
                if ($item->isMultiLang()) {
                    
                    return true;
                }
            });
        }
        
        /**
         * To get only non-multilang elements
         * 
         * @return \Illuminate\Support\Collection
         */
        public function getNonMultilangElements() {
            
            return $this->collection->filter(function(Element $item){
                
                if (!$item->isMultiLang()) {
                    
                    return true;
                }
            });
        }
        
       
}