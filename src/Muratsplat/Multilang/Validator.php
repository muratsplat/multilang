<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;
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
class Validator  implements MessageProviderInterface {
    
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
    
    
   
        public function __construct(MessageBag $message, Larevalidator $validator, Config $config ) {
                           
            $this->message = $message;
            
            $this->validator = $validator;
            
            $this->config = $config;
            
            $this->prefix = $this->config->get('prefix');
            
        }       
        
        /**
	 * Get the messages for the instance.
	 *
	 * @return \Illuminate\Support\MessageBag
	 */
	public function getMessageBag() {
                        
            return $this->message;                        
        }
        
        public function make(Picker $picker, Model $model, array $rules) {
            
            $langModel = $this->getLangModel($model);
            
            $this->mainModel = $model;
            
            $this->picker = $picker;
            
            if (empty($rules)) {
                
                $this->mergerules($model, $langModel);
            }
            
            return $this->validate();
                                            
        }
        
        /**
         * to merged models rules
         *  
         * @param Muratsplat\Multilang\Interfaces\MainInterface $main
         * @param Muratsplat\Multilang\Interfaces\LangInterface $lang
         * @return void
         */
        protected function mergeRules(Model $main, Model $lang) {
            
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
                
                $pos = $this->picker->isMultilang($key);
                
                $rmkey = $this->picker->removePrefixAndId($key, $pos);
                
                if(is_numeric($pos) && array_key_exists($rmkey, $this->mergedrules) ) {
                    
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
                        
            $className = get_class($model) . $this->config->get('appLanguageModel');

            // checking existed translation model 
            if (!class_exists($className , $autoload = true) ) {

                throw new MultiLangModelWasNotFound('Multi language post was detected!'
                       . ' In case of this it needs a model for multi languages content.');

            }
            
            return new $className;           
        }
        
        private function validate() {
            
            $nonMultilang = $this->picker->getMultilang()->toArray();
            
            var_dump($this->picker->getSource());
            
            var_dump($this->rules);           
            
        }

        
        
}
