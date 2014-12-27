<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
//use Illuminate\Config\Repository as Config;
//use Illuminate\Support\Contracts\MessageProviderInterface;
//use Illuminate\Support\MessageBag;
//
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
 * The class make be easy to manage multi language content when
 * it thinks all CRUD process by working on ORM.
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
         * to create new wrapper with main model and multi language models.
         * 
         *  
         * @param Illuminate\Database\Eloquent\Model $mainModel
         * @param Illuminate\Database\Eloquent\Collection $langModels
         * @param Illuminate\Database\Eloquent\Model|int $wantedLang
         * @param Illuminate\Database\Eloquent\Model|int $defaultLang
         * @return \static
         */
        public function createNew(Model $mainModel, Collection $langModels, $wantedLang, $defaultLang) {
            
            $newOne = new static();
            
            $newOne->setMainModel($mainModel)
                    ->setLangModels($langModels)
                    ->setWantedLang($wantedLang)
                    ->setDefaultLang($defaultLang);
            
            return $newOne;           
        }
                
                
    
        
        
        
        
}
