<?php namespace Muratsplat\Multilang;

use Illuminate\Support\Collection;
use Muratsplat\Multilang\Element;
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
         */
        public function import(array $post=array()) {
            
            $this->rawPost = $post;
            
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
                    $cleanedKey = $this->removePrefixAndId($k, $pos);
                    

                    /**
                     * array(Language id => array(post key => post value));
                     */
                    $this->translateFieldById[substr($k, $pos+1, strlen($k))][$cleanedKey] =  $v;
                }    
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
        
        
        protected function createOrUpdate($id, $key, $value) {
            
            foreach ($this->collection->all() as $v) {
                
                if (!$v->multilang) {
                    
                    continue;
                }
                
                if ($v->lang_id === (integer) $id) {
                    
                    $v->$key = $value;
                    
                    return true;
                }                
            }
            
            
        }
        
        protected function create($id,$key, $value, $multilang = false) {
            
            $item = new Element();
            
            $item->$key = $value;
            
            $item->multilang = $multilang;            
            
            $this->collection->push($item);
        }
        
        
        
        
        
        
        
        
        
        
        
        
 
 
 }
