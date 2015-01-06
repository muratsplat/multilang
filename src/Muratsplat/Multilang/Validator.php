<?php namespace Muratsplat\Multilang;

use Illuminate\Config\Repository as Config;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Validation\Factory as Larevalidator;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Base;
use Muratsplat\Multilang\Interfaces\MainInterface;
use Muratsplat\Multilang\Interfaces\LangInterface;

/**
 * Validator Class
 * 
 * This class simple validator for Multi-Language post data.
 * The object will get rules from Main model and Languages Model,
 * and than it will validate rules by looking the post data.
 * 
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class Validator extends Base implements MessageProviderInterface {
    
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
    protected $config;
    
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
    private $rules = [];
        
        /**
         * Constructer
         * 
         * @param Illuminate\Validation\Factory $validator
         * @param Illuminate\Config\Repository $config
         */
        public function __construct(Larevalidator $validator, Config $config) {
                      
            $this->validator = $validator;
            
            $this->config = $config;                      
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
        public function make(Picker $picker, MainInterface $model, array $rules) {
            
            //$langModel = $this->getLangModel($model);
            
            $this->mainModel = $model;
            
            $this->picker = $picker;      
            // setting rules for validations    
            $this->mergeRules($model, $model->langModels()->getRelated(), $rules);     
            
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
        protected function mergeRules(MainInterface $main, LangInterface $lang, array $rules) {
            
            if(!empty($rules)) {
                
                $this->updateRules($rules);
            }
            
            $this->mergeModelRules($main, $lang);
            
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
                
                if($this->inMergedRules($key)) {
                    
                    $this->addRulesIfNotExist($key, $this->mergedrules[$key]);
                    
                    continue;
                }
                // Is it multilang ? True, return language ID,
                $pos = $this->picker->isMultilang($key);
                // deleting the prefix and id is right side of the prefix
                $rmkey = $this->picker->removePrefixAndId($key, $pos);
                
                if(is_numeric($pos) && $this->inMergedRules($rmkey) ) {
                    // re-editing rules for rawPost data..
                    $this->addRulesIfNotExist($key, $this->mergedrules[$rmkey]);                   
                }                
            }           
        }
        
        /**
         * to valitate
         * 
         * @return boolean false, if it is failed.
         */
        private function validate() {            
            
            $v = $this->validator->make($this->picker->getSource(), $this->getRules());
            
            if($v->fails()) {
                // to set errors messages
                $this->message = $v->getMessageBag();
                
                // failed!
                return false;
            }
            // passed!
            return true;            
        }
        
        /**
         * to get rules
         * 
         * @return array
         */
        public function getRules() {
            
            return $this->rules;
        }
        
        /**
         * to update rules for only raw rules.
         *   
         * @param array $rules
         * @return void
         */
        protected function updateRules(array $rules = array()) {
            
            $this->rules = array_merge($this->rules, $rules);            
        }
        
        /**
         * to merged models' rules
         * 
         * @param \Muratsplat\Multilang\Interfaces\MainInterface $main
         * @param \Muratsplat\Multilang\Interfaces\LangInterface $lang
         * @return
         */
        private function mergeModelRules(MainInterface $main, LangInterface $lang) {

            $this->mergedrules = array_merge($main->getRules(), $lang->getRules());
        }
        
        /**
         * to check rule in merged rules which are getted from models.
         * 
         * @param string $key
         * @return bool
         */
        private function inMergedRules($key) {
            
            return array_key_exists($key, $this->mergedrules);
        }   
       
        
        public function addRulesIfNotExist($key, $rule) {
    
            if($this->inRules($key)) { return; } 
                    
            $this->addRules($key, $rule);      
        }
        
        /**
         * To check rule
         * 
         * @param string $key
         * @return bool
         */
        public function inRules($key) {
            
            return array_key_exists($key, $this->rules);
        }
        
        /**
         * To add rule
         * 
         * @param string $key
         * @param string $rule
         */
        protected function addRules($key, $rule) {
            
            $this->rules = array_add($this->rules, $key, $rule);
        }
}
