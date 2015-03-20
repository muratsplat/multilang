<?php namespace Muratsplat\Multilang;

use LogicException;
use RuntimeException;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository as Config;

use Muratsplat\Multilang\Exceptions\WrapperUndefinedProperty;
use Muratsplat\Multilang\Base;
use Muratsplat\Multilang\CheckerAttribute;

/**
 * Wrapper Class
 * 
 * The class make be easy to access main model and multi language models.
 * It can imagened what is such one single model as at one point accessing to main model and
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
class Wrapper extends Base  {
    
    /**
     *  Main model
     * 
     * @var \Illuminate\Database\Eloquent\Model 
     */
    protected $mainModel;
    
    /**
     * Default Language id for models
     *
     * @var int 
     */
    private $defaultLang = 1;
    
    /**
     * Wanted Language id
     * 
     * @var int 
     */
    private $wantedLang = 1;  
    
    /**
     * Collction Object
     *
     * @var Illuminate\Database\Eloquent\Collection
     */
    protected $collection;
    
    /**
     * @var \Muratsplat\Multilang\CheckerAttribute;
     */
    private $checkerAttribute;
    
    /**
     * Force mode is forced to get just wanted lang attribute. 
     *
     * @var bool
     */
    private $force = false;

        /**
         * Constructer
         * 
         * @param Config $config
         */
        public function __construct(Config $config, CheckerAttribute $checker) {
            
            $this->config = $config;
            
            $this->checkerAttribute = $checker;
        }
   
        /**
         * to set main model
         * 
         * @param \Illuminate\Database\Eloquent\Model $mainModel
         * @return \Muratsplat\Multilang\Wrapper
         */
        private function setMainModel(Model $mainModel) {
            
            $this->mainModel = $mainModel;
            
            return $this;
        }
        
        /**
         * to set wanted Language's id. it can pass language model
         * 
         * @param  Illuminate\Database\Eloquent\Model|int $wantedLang
         * @return \Muratsplat\Multilang\Wrapper
         */
        public function setWantedLang($wantedLang) {
           
            $wanted = is_object($wantedLang) ? (integer) $wantedLang->id :(integer) $wantedLang;
            
            /*
             * Wanted Lang can not be zero(0)!
             */
            $this->wantedLang = $wanted > 0 ? $wanted : 1;
            
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
                        
            $default = is_object($defaultLang) ? (integer) $defaultLang->id :(integer) $defaultLang;
            /*
             * Default Lang can not be zero(0)!
             */
            $this->defaultLang = $default > 0 ? $default : 1;
            
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
         * @param Illuminate\Database\Eloquent\Model $mainModel
         * @param Illuminate\Database\Eloquent\Model|int $wantedLang
         * @param Illuminate\Database\Eloquent\Model|int $defaultLang
         * @return \static
         */
        public function createNew(Model $mainModel, $wantedLang= null, $defaultLang = null) {
            
            $wanted  = is_null($wantedLang) ? $this->getWantedLang() : $wantedLang;
            $default = is_null($defaultLang) ? $this->getDefaultLang() : $defaultLang;
            
            $newOne  = new static($this->config, $this->checkerAttribute);
            
            $newOne->setMainModel($mainModel)
                    ->setWantedLang($wanted)
                    ->setDefaultLang($default);
            
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
                
                case $this->isExistedOnMain($name): 
                    
                    return $this->getMainModel()->getAttribute($name);
                
                case $this->isExistedOnLangModel($name) && $this->force:                    
                    
                    return  $this->getAttributeOnLang($name, true);
                                              
                default :
                                        
                    return $this->getAttributeOnLang($name);         
            }
            
              
            throw new WrapperUndefinedProperty("[$name] is undefined property! "
                    . "Called property was not found on 'main model or lang models"); 
        }
        
        /**
         * Magic isset method for this class
         * 
         * @param string $name
         * @return bool
         */
        public function __isset($name) {            
            
            return $this->isExistedOnMain($name) || $this->isExistedOnLangModel($name);
        }
        
        /**
         * to check given attribute is existed on main model
         * 
         * @param string $name attribute nane
         * @return bool
         */
        public function isExistedOnMain($name) {
            
            switch (true) {
                                            
                case $this->checkerAttribute->check($this->getMainModel(), $name);                        
                    
                case method_exists($this->getMainModel(), $name):
                    
                    return true;                    
            }
            
            return false;
        }
        
        /**
         * 
         * @param type $name
         * @return boolean
         */
        public function isExistedOnLangModel($name) {
            
            $langModel = $this->getMainModel()->langModels()->getRelated();
            
            return $this->checkerAttribute->check($langModel, $name);   
        }
        
        /**
         * to get wanted languge model
         * 
         * @return \Illuminate\Database\Eloquent\Model|null
         */
        public function getWantedLangModel() {
            
            return $this->getLangById($this->getWantedLang());  
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
         * To get mutli language model by ID
         * 
         * @param int $id
         * @return \Illuminate\Database\Eloquent\Model|null
         */
        private function getLangById($id) {
            
            $reservedKey = $this->getConfig('reservedAttribute');
           
            return $this->getMainModel()->langModels()->getQuery()
                    ->where($reservedKey, $id)->first();            
        }
        
        /**
         *  Handle dynamic method calls into main model
         * 
         * @param string $name
         * @param array $args
         * @return mixed
         * @throws LogicException
         */
        public function __call($name, $args) {
            
            if (method_exists($this->mainModel, $name)) {
                
                return call_user_func_array(array($this->getMainModel(), $name), $args);
            }
            
            throw new LogicException("$name method is not found on main model!");           
        }
        
        /**
         * getter mainModel property
         * 
         * @return \Illuminate\Database\Eloquent\Model
         * @throws RuntimeException
         */
        public function getMainModel() {
            
            if (is_null($this->mainModel)) {                
                                
                throw new RuntimeException('Main Model is not existed!');                
            }
            
            return $this->mainModel;
        }
        
        /**
         * to changed wanted language and to access wrapper
         * 
         * @param \Illuminate\Database\Eloquent\Model|int $lang
         * @param bool $force wanted only attribute
         * @return \Muratsplat\Multilang\Wrapper
         */
        public function wanted($lang) {
            
            $this->setWantedLang($lang);
            
            $this->force = false;
            
            return $this;
        }
        
        /**
         * to enable force mode to only wanted it
         * 
         * @return \Muratsplat\Multilang\Wrapper
         */
        public function force() {
            
            $this->force = true;
            
            return $this;
        }
        
        /**
         * To get attribute on lang models.
         * 
         * If $force parameter ise true and if 
         * wanted attribute is founded return the value 
         * or not return only null  
         * 
         * @param string $name
         * @param bool $force  it is true, only returns wanted attribute,  
         * @return mixes
         */
        protected function getAttributeOnLang($name, $force = false) {
            
            $value = $this->getAttributeOnWantedLang($name);
            
            if (!is_null($value)) {
                
                return $value;                
            }
   
            return $force ? null : $this->getDefaultLangModel()->getAttribute($name);         
        }
        
        /**
         * to get attibute on wanted lang model
         * 
         * @param string $name attribute name
         * @param mixed|null
         */
        private function getAttributeOnWantedLang($name) {
            
            $wanted = $this->getWantedLangModel();
            
            return is_null($wanted) ? null : $wanted->getAttribute($name);
        }
                
        
        
}