<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Validation\Factory as Larevalidator;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Exceptions\MultiLangModelWasNotFound;

//use Muratsplat\Multilang\Exceptions\MultilangRequiredImplement;
//use Muratsplat\Multilang\Exceptions\ElementUndefinedProperty;
//use Muratsplat\Multilang\Exceptions\PickerUnknownError;
//use Muratsplat\Multilang\Exceptions\PickerError;

/**
 * Validator Class
 * 
 * This class simple validator for Multi-Language post data.
 * The object will get rules from Main model and Languages Model,
 * and than it will validate rules by looking the post data.
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class Validator implements MessageProviderInterface {
    
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
     * Laravel MessageBag Object
     * 
     * @var Illuminate\Support\MessageBag 
     */
    private $message;
    
    /**
     * Laravel Validator Object
     *
     * @var Illuminate\Validation\Factory
     */
    private $validator;
    
    /**
     * Laravel Config Object
     * @var Illuminate\Config\Repository
     */
    private $config;
    
    /**
     * rules for validation
     * 
     * @var array 
     */
    private $mergedrules;
    
    /**
     * Finally rules 
     *  
     * @var array
     */
    private $rules;
    
    /**
     * Prefix for multi languages element
     *
     * @var string 
     */
    private $prefix;
        
        /**
         * Constructer
         * 
         *
         * @param Illuminate\Validation\Factory $validator
         * @param Illuminate\Config\Repository $config
         */
        public function __construct(Larevalidator $validator, Config $config) {
                      
            $this->validator = $validator;
            
            $this->config = $config;
            
            $this->prefix = $this->config->get('multilang::prefix');           
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
         * to validate post data by model's rules or rules in inputted array
         * 
         * @param Muratsplat\Multilang\Picker $picker
         * @param Muratsplat\Multilang\Interfaces\MainInterface $model
         * @param array $rules
         * @return boolean  true, if all rules are passed.
         */
        public function make(Picker $picker, Model $model, array $rules) {
            
            $langModel = $this->getLangModel($model);
            
            $this->mainModel = $model;
            
            $this->picker = $picker;      
            // setting rules for validations    
            $this->mergeRules($model, $langModel, $rules);     
            
            return $this->validate();
                                            
        }
        
        /**
         * to merged models rules
         *  
         * @param Muratsplat\Multilang\Interfaces\MainInterface $main
         * @param Muratsplat\Multilang\Interfaces\LangInterface $lang
         * @param array $rules Rules for validation
         * @return void
         */
        protected function mergeRules(Model $main, Model $lang, array $rules) {
            
            if(!empty($rules)) {
                
                $this->rules = $rules;
                
                return;
            }
            
            $this->mergedrules = array_merge($main->getRules(), $lang->getRules());
            
            $this->updateRulesForRawPost();           
        }
        
        /**
         * to update rules for RawPost.
         * 
         * After rules was merged, validation rules should be re-edited 
         * for rawPost data. Because a multi language element likes be "title@1"
         * and this key is not in currented rules. We need to update rules
         * as for rawpost element's key.
         * 
         * @return void
         */
        private function updateRulesForRawPost() {
                       
            foreach ($this->picker->getSource() as $key => $value) {
                
                if(array_key_exists($key, $this->mergedrules)) {
                    
                    $this->rules[$key] = $this->mergedrules[$key];
                    
                    continue;
                }
                // Has the key multilang prefix?
                $pos = $this->picker->isMultilang($key);
                // deleting the prefix and id is right side of the prefix
                $rmkey = $this->picker->removePrefixAndId($key, $pos);
                
                if(is_numeric($pos) && array_key_exists($rmkey, $this->mergedrules) ) {
                    // re-editing rules for rawPost data..
                    $this->rules[$key] = $this->mergedrules[$rmkey];                    
                    
                }
                
            }        
            
        }

        /**
         * to get multilang model by using main model 
         * 
         * @param Illuminate\Database\Eloquent\Model
         * @return Illuminate\Database\Eloquent\Model
         * @throws MultiLangModelWasNotFound
         */
        private function getLangModel(Model $model) {
                        
            $className = get_class($model) . $this->config->get('multilang::appLanguageModel');

            // checking existed translation model 
            if (!class_exists($className , $autoload = true) ) {

                throw new MultiLangModelWasNotFound('Multi language post was detected!'
                       . ' In case of this it needs a model for multi languages content.');
            }            
            return new $className;           
        }
        
        /**
         * to valitate
         * 
         * @return boolean false, if it is failed.
         */
        private function validate() {            
            
            $v = $this->validator->make($this->picker->getSource(), $this->mergedrules);
            
            if($v->fails()) {
                // to set errors messages
                $this->message = $v->getMessageBag();
                
                // failed!
                return false;
            }
            // passed!
            return true;            
        }       
}
