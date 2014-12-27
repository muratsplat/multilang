<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
//use Illuminate\Config\Repository as Config;
//use Illuminate\Support\Contracts\MessageProviderInterface;
//use Illuminate\Support\MessageBag;

use Muratsplat\Multilang\Exceptions\WrapperUndefinedProperty;
//use Muratsplat\Multilang\Picker;
//use Muratsplat\Multilang\Interfaces\MainInterface;
//use Muratsplat\Multilang\Exceptions\MultilangRequiredImplement;
//use Muratsplat\Multilang\Validator;
//use Muratsplat\Multilang\Exceptions\MultiLangModelWasNotFound;
//use Muratsplat\Multilang\Exceptions\RelationNotCorrect;
//use Muratsplat\Multilang\Exceptions\MultilangPostEmpty;
//use Muratsplat\Multilang\Exceptions\ElementUndefinedProperty;
//use Muratsplat\Multilang\Exceptions\PickerUnknownError;
//use Muratsplat\Multilang\Exceptions\PickerError;

/**
 * Wrapper Class
 * 
 * The class make be easy to access main model and multi language models.
 * It can imagened what is such one single model as at one point accsessing to main model and
 * multi language models.
 * 
 * We have Page model and PageLang model. In normally it can access to child language models
 * like this:
 *  ---
 *    foreach($page->PageLangs as $lang) {
 * 
 *       if($lang->lang_id === $wantedId) {
 *            return $lang->title; // return Foo;
 *        }
 *   
 *        return null;
 *    }
 *  --- 
 *  
 *  
 * In same example by using wrapper:
 *  ---
 *  $page = new Wrapper();
 *  $wrapper = $Wrapper->createNew($page, $langs, $wantedId, $defaultId);
 *  $wrapper->title; Return Foo
 *  ---
 *  
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class Wrapper  {
    
    /**
     *  Main model
     * 
     * @var \Illuminate\Database\Eloquent\Model 
     */
    protected $mainModel;
    
    /**
     * Multi Language Models releation to main model
     *  
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $langModel;
    
    /**
     * Default Language id for models
     *
     * @var int 
     */
    private $defaultLang;
    
    /**
     * Wanted Language id
     * 
     * @var int 
     */
    private $wantedLang;    
    
    /**
     *
     * @var array
     */
    protected $collection;
    
        /**
         * Constructer
         * 
         * @param array $items
         */
        public function __construct(array $items= array()) {
            
            $this->collection = $items;
        }
   
        /**
         * to set main model
         * 
         * @param \Illuminate\Database\Eloquent\Model $mainModel
         * @return \Muratsplat\Multilang\Wrapper
         */
        public function setMainModel(Model $mainModel) {
            
            $this->mainModel = $mainModel;
            
            return $this;
        }
        
        /**
         * to set multi language models in Collection object
         * 
         * @param \Illuminate\Database\Eloquent\Collection $langModels
         * @return \Muratsplat\Multilang\Wrapper
         */
        public function setLangModels(Collection $langModels) {
            
            $this->langModel = $langModels;
            
            return $this;
        }
        
        /**
         * to set wanted Language's id. it can pass language model
         * 
         * @param  Illuminate\Database\Eloquent\Model|int $wantedLang
         * @return \Muratsplat\Multilang\Wrapper
         */
        public function setWantedLang($wantedLang) {
            
            $this->wantedLang = is_object($wantedLang) ? $wantedLang->id :(integer) $wantedLang;
            
            return $this;
        }
        
        /**
         * to set default Language's id. it can pass language model
         * 
         * In generally Laravel Apps has a model for managing supported languages.
         * In the app default language's model can be prameter to set it. It is optional
         * to passed language model. 
         *   
         * @param  Illuminate\Database\Eloquent\Model|int $defaultLang
         * @return \Muratsplat\Multilang\Wrapper
         */
        public function setDefaultLang($defaultLang) {
            
            $this->defaultLang = is_object($defaultLang) ? $defaultLang->id :(integer) $defaultLang;
            
            return $this;
        }
        
        /**
         * to get default language ID
         * 
         * @return int Language ID
         */
        public function getDefaultLang() {
            
            return (integer) $this->defaultLang;
        }
        
        /**
         * to get wanted language ID
         * 
         * @return int Language ID
         */
        public function getWantedLang() {
            
            return (integer) $this->wantedLang;
        }
        
        /**
         * to create new wrapper with main model and multi language models.
         * 
         *  
         * @param Illuminate\Database\Eloquent\Model $mainModel
         * @param Illuminate\Database\Eloquent\Collection $langModels
         * @param Illuminate\Database\Eloquent\Model|int $wantedLang
         * @param Illuminate\Database\Eloquent\Model|int $defaultLang
         * @return \static
         */
        public function createNew(Model $mainModel, Collection $langModels, $wantedLang=1, $defaultLang=1) {
            
            $newOne = new static();
            
            $newOne->setMainModel($mainModel)
                    ->setLangModels($langModels)
                    ->setWantedLang($wantedLang)
                    ->setDefaultLang($defaultLang);
            
            return $newOne;           
        }
        
        /**
         * to get overloaded property by looking main model and lang models
         * 
         * @param string $name
         * @return mixed
         * @throws \Muratsplat\Multilang\Exceptions\WrapperUndefinedProperty
         */
        public function __get($name) {
            
            switch (true) {
                
                case $this->isExistedOnMain($name): return $this->mainModel->getAttribute($name);
                
                case $this->isExistedOnLangModel($name): return $this->getWantedLangModel()->getAttribute($name);
                                              
                default :
                    
                     if(!array_key_exists($name, $this->getDefaultLangModel()->getAttributes())) {
                        
                         throw new WrapperUndefinedProperty("[$name] is undefined property! Called property was not found on "
                                 . 'main model or lang models'); 
                     }
                     
                     return $this->getDefaultLangModel()->getAttribute($name);            
            }
        }
        
        /**
         * Magic isset method for this class
         * 
         * @param string $name
         * @return bool
         */
        public function __isset($name) {
            
            switch (true) {
                
                case $this->isExistedOnMain($name): 
                    
                    return array_key_exists($name, $this->mainModel->getAttributes());
                
                case $this->isExistedOnLangModel($name): 
                    
                    return array_key_exists($name, $this->getWantedLangModel()->getAttributes());
                                              
                default :
                    
                    return !array_key_exists($name, $this->getDefaultLangModel()->getAttributes()) ? false : true;            
            }
        }
        
        /**
         * to check given attribute name on main model
         * 
         * @param string $name
         * @return bool
         */
        public function isExistedOnMain($name) {
            
            return array_key_exists($name, $this->mainModel->getAttributes());
        }
        
        /**
         * 
         * @param type $name
         * @return boolean
         */
        public function isExistedOnLangModel($name) {
            
            $lang = $this->getWantedLangModel();
            
            if(is_null($lang)) {
                
                return false;
            }
            
            return array_key_exists($name, $lang->getAttributes());   
        }
        
        /**
         * to get wanted languge model
         * 
         * @return \Illuminate\Database\Eloquent\Model|null
         */
        public function getWantedLangModel() {
            
            return $this->getLangById($this->wantedLang);  
        }
        
        
        /**
         * to get default lang model
         * 
         * @return \Illuminate\Database\Eloquent\Model|null 
         */
        protected function getDefaultLangModel() {
            
            $result = $this->getLangById($this->defaultLang);
            
            if(is_null($result)) {
                
                throw new WrapperUndefinedProperty("Default langugage was not founded!. "
                        . "take require one language model record at the very least!.");
            }
            return $result;
        }
        
        /**
         * To get lang model by ID
         * 
         * @param int $id
         * @return \Illuminate\Database\Eloquent\Model|null
         */
        private function getLangById($id) {
            
            return $this->langModel->filter(function($item) use ($id) {
                
                return (integer) $item->__lang_id__ === (integer) $id;
                
            })->first();            
        }
        
                
                
    
        
        
        
        
}