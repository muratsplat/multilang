<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;

use Muratsplat\Multilang\Picker;
use Muratsplat\Multilang\Interfaces\MainInterface;
use Muratsplat\Multilang\Exceptions\MultilangRequiredImplement;
use Muratsplat\Multilang\Validator;
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
     * an prefix to pick multi-language elements up
     * 
     * @var string 
     */
    private $prefix;
    
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
                
        public function create(array $post, Model $model) {            
                
            $this->checkMainImplement($model);
            
            $this->picker->import($post);           
        }
        
        public function update(array $post, Model $model) {            
                
            $this->checkMainImplement($model);
            
            $this->picker->import($post);           
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
}
