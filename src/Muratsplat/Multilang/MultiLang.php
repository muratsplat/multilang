<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Events\Dispatcher;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Base;
use Muratsplat\Multilang\Interfaces\MainInterface;
use Muratsplat\Multilang\Validator;
use Muratsplat\Multilang\Wrapper;
use Muratsplat\Multilang\Exceptions\MultilangPostEmpty;
use Muratsplat\Multilang\Exceptions\MultilangRequiredImplement;
use Muratsplat\Multilang\Exceptions\MultilangParameterInvalid;
use Muratsplat\Multilang\Exceptions\MultiLangModelWasNotFound;


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
    private $deletedMainModel;
    
    /**
     * Wrapper to access to main model and mutli language model 
     * by one wrapper object
     *
     * @var \Muratsplat\Multilang\Wrapper 
     */
    private $wrapper;
    
    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Events\Dispatcher
     */
    protected static $distpatcher;

        /**
         * Constructer
         * 
         * @param \Muratsplat\Multilang\Picker $picker
         * @param \Illuminate\Config\Repository $config
         * @param \Illuminate\Support\Contracts\MessageProviderInterface $message
         * @param \Muratsplat\Multilang\Validator $validator
         * @param \Muratsplat\Multilang\Wrapper $wrapper
         */
        public function __construct(
                Picker      $picker, 
                Config      $config, 
                MessageBag  $message, 
                Validator   $validator, 
                Wrapper     $wrapper) {
            
            $this->picker   = $picker;                       
            $this->config   = $config;            
            $this->message  = $message;            
            $this->validator= $validator;            
            $this->wrapper  = $wrapper;
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
            
            $results = $this->getLangModels()->createMany($posts);
            
            return !empty($results);    
        }

        /**
         * Simple switcher is as for on created or updated or deleted action
         * 
         * @param bool  $mainModel If it wants to langModels, passes false
         * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Model
         */
        protected function switcher($mainModel=true) {
           
            // we can say simple hub to access lang models for this.
            switch (true) {
                
                case !is_null($this->updatedMainModel) : 
                    
                    return $mainModel ? $this->updatedMainModel : $this->updatedMainModel->langModels();
                    
                case !is_null($this->createdMainModel) : 
                    
                    return $mainModel ? $this->createdMainModel : $this->createdMainModel->langModels();
                    
                case !is_null($this->deletedMainModel) : 
                    
                    return $mainModel ? $this->deletedMainModel : $this->deletedMainModel->langModels();
            }
            
            throw new MultiLangModelWasNotFound('the model/models could not found on any actions!');
        }        
        
        /**
         * to update main model and multi-languages models by using multilang post
         *  
         * @param array $post post data
         * @param Illuminate\Database\Eloquent\Model $model
         * @param array $rules optional, you may want to add new rules
         * @return boolean
         */
        public function update(array $post, Model $model, array $rules=array()) {
            
            if (!$this->checkdata($post, $model, $rules)) { return false;}
            
            if (!$model->exists) {
                
                throw new MultiLangModelWasNotFound('Model is not existed, therefore it can not updated!');
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
                                
                $existed = $this->existedInLangs($v[$this->getLangIdKey()]);
                
                if(!is_null($existed)) {
                    
                    $existed->update($v);
                  
                    continue;
                }
                
                $this->getLangModels()->create($v);                
            }
                                        
            return $this->elementsEqualsToLangModel();          
        }
        
        /**
         * to get lang model if it is existed by looking id
         * 
         * @param int $id
         * @return \Illuminate\Database\Eloquent\Model|null null, if model is not existed
         */
        private function existedInLangs($id) {
                       
            $existed = $this->getLangModels()->getResults()->filter(function($item) use($id) {
                
                if ((integer) $id === (integer) $item->{$this->getLangIdKey()}) {
                    
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
                               
                if(is_null($this->picker->getById($item->{$this->getLangIdKey()}))) {
                    
                      $item->delete();               
                }               
            };
            
            $this->getLangModels()->getQuery()->get()->each($callback);    
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
            
            return $this->picker->getMultilang()->count() === $this->getLangModels()->getQuery()->get()->count();
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
                
        /**
         * to delete model with multi language models..
         * 
         * @param Model $model it will be deleted!
         * @return boolean
         */
        public function delete(Model $model) {          
             
            if (!$model->exists) {
                
                throw new MultiLangModelWasNotFound('Model is not existed, therefore it can not deleted!!');
            }
            
            $this->checkMainImplement($model);
            // we have to save model the property of object for each methods
            $this->deletedMainModel = $model;
            
            if ($this->deletedMainModel->isMultilang()) {
                
                return $this->deleteAllLangs() && $this->deletedMainModel->delete();               
            }            
            return $this->deletedMainModel->delete();          
        }
        
        /**
         * to delete all multi language models
         * 
         * @return bool
         */
        private function deleteAllLangs() {
                
            $this->getLangModels()->getResults()->each(function($item) {
              
                $item->delete();
            });
            
            return $this->getLangModels()->getResults()->count() === 0;   
        }
        
        /**
         * to set main model
         * 
         * @param \Muratsplat\Multilang\Interfaces\MainInterface $model
         */
        public function setMainModel(MainInterface $model) {
            
            $this->mainModel = $model;
        }
        
        /**
         * To make a wrapper that includes main and language models.
         * If parameter is Eloquent Collection, all of it is converted single
         * wrapper and than new Collection is created by pussing created new wrappers.
         * You can use returned Collection like to use Eloquent Collection.
         * 
         * @param Illuminate\Database\Eloquent\Collection|Illuminate\Database\Eloquent\Model $model
         * @param Illuminate\Database\Eloquent\Model|int $wantedLang language id or specific language model
         * @param Illuminate\Database\Eloquent\Model|int $defaultLang language id or specific language model
         * @return \Muratsplat\Multilang\Wrapper|Illuminate\Database\Eloquent\Collection
         */
        public function makeWrapper($model, $wantedLang=null, $defaultLang=null) {
            // firing creating wrapper event!
            $this->fireWrapper('creating');
            
            if ($model instanceof Model) {
                
                $this->setMainModel($model);
                    
                return $this->createWrapper($wantedLang, $defaultLang);      
            }
            
            if ($model instanceof Collection) {
                
                return $this->createWrappersInCollection($model, $wantedLang, $defaultLang);           
            }
            
            throw new MultilangParameterInvalid('First parameter only can be Model or Collection');
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
        
        /**
         * to get lang models
         * 
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function getLangModels() {
           
           return $this->switcher(false);
        }
        
        /**
         * to get main model
         * 
         * @return \Illuminate\Database\Eloquent\Model
         */
        public function getMainModel() {
           
           return $this->switcher();
        }
        
        /**
         * to get  Language Id Key as attribute name on model
         * 
         * @return string
         */
        private function getLangIdKey() {
            
            return $this->getConfig('reservedAttribute');
        }
        
        /**
         * to get wrapper object
         * 
         * @return \Muratsplat\Multilang\Wrapper
         */
        public function getWrapperInstance() {
            
            return $this->wrapper;
        }
                
	/**
	 * Set the event dispatcher instance.
         * 
         * Note: This method was copied from Laravel Eloquent's Model class!!!
	 *
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public static function setEventDispatcher(Dispatcher $dispatcher) {
            
		static::$distpatcher = $dispatcher;
	}
        
        /**
         * to fire the given event
         * 
         * @param string $event
         * @return mixed
         */
        protected function fireWrapper($event) {
            
            if (!isset(static::$distpatcher)) {return;}
            
            $fullEventName = 'multilang.wrapper.' . $event;            
            
            return static::$distpatcher->fire($fullEventName, $this);           
        }
        
}
