<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Collection;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Base;
use Muratsplat\Multilang\Interfaces\MainInterface;
use Muratsplat\Multilang\Exceptions\MultilangRequiredImplement;
use Muratsplat\Multilang\Validator;
use Muratsplat\Multilang\Wrapper;
use Muratsplat\Multilang\Exceptions\MultilangPostEmpty;

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
 * @package Multilang
 */
class MultiLang extends Base implements MessageProviderInterface {
    
   /**
    * Main Model  
    * 
    * @var Illuminate\Database\Eloquent\Model
    */
    private $mainModel;
    
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
    protected $config;
    
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
     * Model will be updated.
     *  
     * @var Illuminate\Database\Eloquent\Model 
     */
    private $updatedMainModel;
    
    /**
     * Model will be deleted.
     * 
     * @var array 
     */
    private $deletedManinModel;
    
    /**
     * Wrapper to access to main model and mutli language model 
     * by one wrapper object
     *
     * @var \Muratsplat\Multilang\Wrapper 
     */
    private $wrapper;
    
        /**
         * Consructer
         * 
         * @param \Muratsplat\Multilang\Picker $picker
         * @param \Illuminate\Config\Repository $config
         * @param \Illuminate\Support\Contracts\MessageProviderInterface $message
         * @param \Muratsplat\Multilang\Validator $validator
         * @param \Muratsplat\Multilang\Wrapper $wrapper
         */
        public function __construct(Picker $picker, Config $config, MessageBag $message, Validator $validator, Wrapper $wrapper) {
            
            $this->picker = $picker;
                       
            $this->config = $config;
            
            $this->message = $message;
            
            $this->validator= $validator;
            
            $this->wrapper = $wrapper;
        }        
        
        /**
         * to create main model and multi-languages models by using multilang post
         * which is made by Picker Class
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
            
            $this->setMainModel($model);
            
            if (!$this->picker->isPostMultiLang()) {
                
                return $this->createMainModel($model);
            }
                       
            return $this->createMainModel($model) && $this->createLangModels();         
        }
        
        /**
         * to create main model
         * 
         * @return boolean
         */
        protected function createMainModel(Model $model) {
                       
            $post = $this->picker->getNonMultilangToArray();
                        
            $this->createdMainModel = $model->create($post);
                           
            return $this->createdMainModel->save();
        } 
        
        /**
         * to create lang model for multi-language content
         * 
         * @return boolean
         */
        protected function createLangModels() {
             
            $posts = $this->picker->getMultilangToArray();
            
            $this->LangModels()->createMany($posts);     
    
            return true;
        }
        
//        /**
//         * Created new main model's multi language model
//         * 
//         * @throws \Muratsplat\Multilang\Exceptions\RelationNotCorrect
//         */
//        private function checkRelation() {
//            
//            // we have to sure everything is ok!!
//              
//            $nameLang = $this->getLangModelName();
//            
//            if (!$this->mainModel->langModels()->getRelated() instanceof $nameLang) {
//                
//                throw new RelationNotCorrect("It looks the relation is not correct "
//                        . "between main model and multi-language models");                
//            }          
//        }
//        
        /**
         * to connect multi-language model
         * Simple switcher is on created or updated
         * 
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        protected function langModels() {
           
            // we can say simple hub to access lang models for this.
            switch (true) {
                
                case !is_null($this->updatedMainModel) : return $this->updatedMainModel->langModels();
                    
                case !is_null($this->createdMainModel) : return $this->createdMainModel->langModels();
                    
                case !is_null($this->deletedManinModel) : return $this->deletedManinModel->langModels();                
            }        
        }        
        
        /**
         * 
         * @param array $post post data
         * @param Illuminate\Database\Eloquent\Model $model
         * @param array $rules optional, you may want to add new rules
         * @return boolean
         */
        public function update(array $post, Model $model, array $rules=array()) {
            
            if (!$this->checkdata($post, $model, $rules) || !$model->exists) {
                
                $this->message->add('logicError', 'Model is not existed, therefore it can not updated!');
                
                return false;   
            }
            
            $this->setMainModel($model);
            // it will make the model to update later
            $this->updatedMainModel = $model;
            
            if(!$this->picker->isPostMultiLang()) {
                
                return $this->updateMainModel();
            }
            
            return $this->updateMainModel() && $this->updateLangModels();            
        }
        
        /**
         * to update main model
         * 
         * @return bool
         */
        protected function updateMainModel() {
                                   
            $result = $this->updatedMainModel->update($this->picker->getNonMultilangToArray());
            
            return is_int($result) && $result >= 1 ? true : $result;
        }
        
        /**
         * to update lang model for multi-language content
         * 
         * @return boolean
         */
        protected function updateLangModels() {           

            foreach ($this->picker->getMultilangToArray() as $v) {
                
                $existed = $this->existedInLangs($v[$this->getConfig('reservedAttribute')]);
                
                if(!is_null($existed)) {
                    
                    $existed->update($v);
                  
                    continue;
                }
                
                $this->langModels()->create($v);            
            }
            
            // deleteing all model is not existed in the post data!
            $this->cleanModelsNotInPost();
            
            return $this->elementsEqualsToLangModel();          
        }
        
        /**
         * to get lang model if it is existed by looking id
         * 
         * @param int $id
         * @return null|Illuminate\Database\Eloquent\Model null, if model is not existed
         */
        private function existedInLangs($id) {
            
            $langIdKey = $this->getConfig('reservedAttribute');
                        
            $existed = $this->langModels()->getResults()->filter(function($item) use ($id, $langIdKey) {
                
                if((integer) $item->getAttribute($langIdKey) === (integer) $id) {
                    
                    return true;                    
                }
            });
       
            return $existed->count() === 0 ? null : $existed->first();    
        }
        
        /**
         * to delete lang model is not in post
         * 
         * @return void 
         */
        private function cleanModelsNotInPost() {
         
            $callback = function($item) {
                
                if(is_null($this->picker->getById($item->id))) {
                 
                    $item->delete();               
                }               
            };
            
            $this->langModels()->getResults()->each($callback);           
        }

        /*
         * To make bu sure what all multi language elements
         * are existed in lang model collections
         * 
         * @return bool
         */
        private function elementsEqualsToLangModel() {
            
            $this->cleanModelsNotInPost();
         
            // the nummber of multi language elements in post must be equal to ones in
            // the number of langauge model collections. So It can be sure everything
            // is ok by the result
            return $this->picker->getMultilang()->count() === $this->langModels()->getResults()->count();
        }        
        
        /**
         * to check required implement and import post data. In addition,
         * validate post data.
         * 
         * @param array $post Post Data. It usualy is be array
         * @param Muratsplat\Multilang\Interfaces\MainInterface $model main model
         * @param array $rules not required. Laravel validation rules in a array
         * @return bool true, it is on success
         */
        protected function checkdata(array $post, Model $model, array $rules) {
                        
            $this->checkMainImplement($model);
            // if post is empty, something must be wrong!!
            if(empty($post)) { 
                
                throw new MultilangPostEmpty('Post Data is empty, '
                        . 'MultiLang needs to acceptable Post Data!');
            }
            
            $this->picker->import($post);
            
            if(!$this->validateAll($model, $rules)) {
                                            
                return false;                
            }
            
            return true;
        }
        
        /**
         * to validate post data with multi language content
         * 
         * @param Muratsplat\Multilang\Interfaces\MainInterface $model
         * @param array $rules
         * @return bool  true, if it is on success
         */
        private function validateAll(Model $model, array $rules) {
            
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
        
//        /**
//         * To get the name of main model's multi language model
//         * 
//         * @return string
//         * @throws Exception
//         */
//        public function getLangModelName() {
//
//            // To create a name of translation model
//            $className = get_class($this->mainModel) . $this->getModelPrefix();
//
//            // checking existed translation model 
//            if (!class_exists($className , $autoload = true) ) {
//
//               throw new MultiLangModelWasNotFound('Multilanguage post was detected! '
//                       . 'In case of this it needs a model for multi language content.');
//            }
//             
//            return $classNam;
//        }
//        
//        /** 
//         * to get relation name to connect hasMany relalation
//         * between main model and lang model
//         * 
//         * @return string 
//         */
//        public function getRelationName() {
//            // for namespace
//            $isNameSpace = explode("\\", $this->getLangModelName());
//            
//            $num = count($isNameSpace);
//            
//            if($num > 1) {
//                
//                return $isNameSpace[$num-1] . 's';
//            }
//
//            return $isNameSpace[0] . 's';
//        }
//        
//        /**
//         * to get model prefix for Multilang contents
//         * 
//         * @return string
//         */
//        private function getModelPrefix(){
//            
//            $prefix = $this->config->get('multilang::modelPrefix');
//            
//            return is_null($prefix) || (strlen(trim($prefix)) === 0) ? $this->modelPrefix : $prefix; 
//        }
        
        /**
         * to delete model with multi language models..
         * 
         * @param Model $model it will be deleted!
         * @return boolean
         */
        public function delete(Model $model) {          
             
            if (!$model->exists) {
                
                $this->message->add('logicError', 'Model is not existed, therefore it can not deleted!');
                
                return false;   
            }
            
            $this->checkMainImplement($model);
            // we have to save model the property of object for each methods
            $this->deletedManinModel = $model;
            
            if ($this->deletedManinModel->isMultilang()) {
                
                return $this->deleteAllLangs() && $this->deletedManinModel->delete();               
            }            
            return $this->deletedManinModel->delete();          
        }
        
        /**
         * to delete all multi language models
         * 
         * @return bool
         */
        private function deleteAllLangs() {
                
            $this->langModels()->getResults()->each(function($item) {
              
                $item->delete();
            });
            
            return $this->langModels()->getResults()->count() === 0;   
        }
        
        /**
         * to set main model
         * 
         * @param Illuminate\Database\Eloquent\Model $model
         */
        public function setMainModel(Model $model) {
            
            $this->mainModel = $model;
        }
        
        /**
         * To make a wrapper that includes main and language models.
         * If parameter is Eloquent Collection, all of it is converted single
         * wrapper and than new Collection is created by pussing created new wrappers.
         * You can use returned Collection like to use Eloquent Collection.
         * 
         * @param Illuminate\Database\Eloquent\Collection|Illuminate\Database\Eloquent\Model $model
         * @para Illuminate\Database\Eloquent\Model|int $wantedLang language id or specific language model
         * @param Illuminate\Database\Eloquent\Model|int $defaultLang language id or specific language model
         * @return \Muratsplat\Multilang\Wrapper|Illuminate\Database\Eloquent\Collection| null null, if it is failed.
         */
        public function makeWarapper($model, $wantedLang=1, $defaultLang=1) {
            
            if ($model instanceof Model) {
                
                $this->setMainModel($model);
                    
                return $this->createWrapper($wantedLang, $defaultLang);               
            }
            
            if ($model instanceof Collection) {
                
                return $this->createWrappersInCollection($model, $wantedLang, $defaultLang);           
            }
            
            return null;        
        }
        
        /**
         * To create a wrapper
         * 
         * @param type $wantedLang
         * @param type $defaultLang
         * @return \Muratsplat\Multilang\Wrapper
         */
        protected function createWrapper($wantedLang, $defaultLang) {
            
            return $this->wrapper->createNew($this->mainModel, $wantedLang, $defaultLang);
        }
        
        /**
         * to create a lot of wrapper by using models in Eloquent Collection
         * 
         * @param Illuminate\Database\Eloquent\Collection $collection
         * @param Illuminate\Database\Eloquent\Model|int $wantedLang language id or specific language model
         * @param Illuminate\Database\Eloquent\Model|int $defaultLang language id or specific language model
         * @return Illuminate\Database\Eloquent\Collection
         */
        protected function createWrappersInCollection(Collection $collection, $wantedLang, $defaultLang) {
            
            $newCollection = $collection->make(array());
            
            while (!$collection->isEmpty()) {
                
                $newCollection->add($this->wrapper->createNew($collection->shift(), $wantedLang, $defaultLang));
            }
            
            return $newCollection;      
        }  
        
        /**
         * To get instance of current Multilang
         * 
         * @return \Muratsplat\Multilang\MultiLang
         */
        public function getInstance() {
            
            return $this;
            
        }
}
