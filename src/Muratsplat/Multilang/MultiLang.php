<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Interfaces\MainInterface;
use Muratsplat\Multilang\Exceptions\MultilangRequiredImplement;
use Muratsplat\Multilang\Validator;
use Muratsplat\Multilang\Exceptions\MultiLangModelWasNotFound;
use Muratsplat\Multilang\Exceptions\RelationNotCorrect;
//use Muratsplat\Multilang\Exceptions\ElementUndefinedProperty;
//use Muratsplat\Multilang\Exceptions\PickerUnknownError;
//use Muratsplat\Multilang\Exceptions\PickerError;

/**
 * MultiLang Class
 * 
 * The class make be easy to manage multi language content when
 * it thinks all CRUD process by working on ORM.
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class MultiLang implements MessageProviderInterface {
    
   /**
    * Main Model  
    * 
    * @var Illuminate\Database\Eloquent\Model
    */
    private $mainModel;
    
    /**
     * Language Model
     * 
     * @var Illuminate\Database\Eloquent\Model 
     */
    private $langModel;
    
    /**
     * Picker Object
     * 
     * @var Muratsplat\Multilang\Picker 
     */
    private $picker;
      
    /** 
     * Laravel Config Object
     *
     * @var Illuminate\Config\Repositor 
     */
    private $config;
    
    /**
     * Laravel MessageBag Object
     * 
     * @var Illuminate\Support\MessageBag 
     */
    private $message;
    
    /**
     * Validator Object
     * 
     * @var Muratsplat\Multilang\Validator
     */
    private $validator;
    
    /**
     * For saving instance of created main model
     *
     * @var Illuminate\Database\Eloquent\Model 
     */
    private $createdMainModel;
    
    /**
     * An prefix to get model For Multi Language contents
     * 
     * @var string 
     */
    private $modelPrefix = "Lang";
    
        /**
         * Constructer
         * 
         * @param Picker $picker
         * @param Model $model
         * @param Config $config
         */
        public function __construct(Picker $picker, Model $model, Config $config, MessageBag $message, Validator $validator) {
            
            $this->picker = $picker;
            
            $this->mainModel = $model;
            
            $this->config = $config;
            
            $this->message = $message;
            
            $this->validator= $validator;          
        }        
        
        /**
         * to create main model and multi-languages models by using multilang post
         * made by Picker Class
         * 
         * @param array $post
         * @param Illuminate\Database\Eloquent\Model $model
         * @param array $rules if it is empty rules will be gotten from models
         * @return boolean
         */
        public function create(array $post, Model $model, array $rules=array()) {  
                      
            if (!$this->checkdata($post, $model, $rules)) {
                
                return false;   
            }
            
            if (!$this->picker->isPostMultiLang()) {
                
                return $this->createMainModel();
            }
                       
            return $this->createMainModel() && $this->createLangModels();
         
        }
        
        /**
         * to create main model
         * 
         * @return boolean
         */
        protected function createMainModel() {
            
            $post = $this->picker->getNonMultilangToArray();
                        
            $this->createdMainModel = $this->mainModel->create($post);
                           
            return $this->createdMainModel->save();
        } 
        
        /**
         * to create lang mode for multi-language content
         * 
         * @return boolean
         */
        protected function createLangModels() {
             
            $posts = $this->picker->getMultilangToArray();
            
            $this->LangModel()->createMany($posts);     
    
            return true;
        }
        
        /**
         * Created new main model's multi language model
         * 
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         * @throws \Muratsplat\Multilang\Exceptions\RelationNotCorrect
         */
        protected function LangModel() {
            
            // we have to sure everything is ok!!
            $nameLangModel = $this->getRelationMethodName() .'s';
                 
            $relatedModel = $this->mainModel->$nameLangModel()->getRelated();
            
            $nameMain = get_class($this->mainModel);
            
            $nameLang = $this->getLangModelName();
            
            if (!$relatedModel instanceof $nameLang) {
                
                throw new RelationNotCorrect("It looks the relation is not correct between main model which is "
                        . "[$nameMain] and [$nameLang] that is multi-language model");    
                
            }
            
            return $this->createdMainModel->$nameLangModel();            
            
        }
        
        
        public function update(array $post, Model $model, array $rules=array()) {
            
            
        }
        
        /**
         * to check required implement and import post data. In addition,
         * validate post data.
         * 
         * @param array $post Post Data. It usualy is be array
         * @param Muratsplat\Multilang\Interfaces\MainInterface $model
         * @param array $rules not required. Laravel validation rules in a array
         * @return boolean true, it is on success
         */
        protected function checkdata(array $post, Model $model, array $rules) {
            
            $this->checkMainImplement($model);
            
            $this->picker->import($post);
            
            if(!$this->validateAll($this->picker, $model, $rules)) {
                                            
                return false;                
            }
            
            return true;           
        }
        
        /**
         * to validate post data with multi language content
         * 
         * @param Muratsplat\Multilang\Picker $picker
         * @param Muratsplat\Multilang\Interfaces\MainInterface $model
         * @param array $rules
         * @return boolean  true, if it is on success
         */
        private function validateAll(Picker $picker, Model $model, array $rules) {
            
            if(!$this->validator->make($this->picker, $model, $rules)) {
                
                $this->message = $this->validator->getMessageBag();
                
                return false;               
            }
            
            return true;
            
        }
        
        /**
         * to check the implement for main model
         * 
         * @param Illuminate\Database\Eloquent\Model $model
         * @return void
         * @throws Muratsplat\Multilang\Exceptions\MultilangRequiredImplement
         */
        private function checkMainImplement($model) {
            
            if(! $model instanceof MainInterface ) {
                
              throw new MultilangRequiredImplement("Muratsplat\Multilang\MainInterface"
                      . "must be implement by your main model!");
              
            }
        }
        
        /**
	 * Get the messages for the instance.
	 *
	 * @return \Illuminate\Support\MessageBag
	 */
	public function getMessageBag() {
                        
            return $this->message;                        
        }
        
        /**
         * To get the name of main model's multi language model
         * 
         * @return string
         * @throws Exception
         */
        public function getLangModelName() {

            // To create a name of translation model
            $className = get_class($this->mainModel) . $this->getModelPrefix();

            // checking existed translation model 
            if (!class_exists($className , $autoload = true) ) {

               throw new MultiLangModelWasNotFound('Multilanguage post was detected! '
                       . 'In case of this it needs a model for multi language content.');

            }
             
            return $className;
        }
        
        public function getRelationMethodName() {
            
            $name = $this->getLangModelName();
            
            $isNameSpace = explode("\\", $name);
            
            $num = count($isNameSpace);
            
            if($num > 1) {
                
                return $isNameSpace[$num-1];
            }

            return $name;
        }
        
        /**
         * to get model prefix for Multilang contents
         * 
         * @return string
         */
        private function getModelPrefix(){
            
            $prefix = $this->config->get('modelPrefix');
            
            return is_null($prefix) || (strlen(trim($prefix)) === 0) ? $this->modelPrefix : $prefix; 
        }
        
}
