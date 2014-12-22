<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Factory as Larevalidator;
use Muratsplat\Multilang\Picker;



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
    
   
        public function __construct(MessageBag $message, Larevalidator $validator,Config $config ) {
                           
            $this->message = $message;
            
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
        
        public function make(Picker $picker, Model $model) {
            
            
        }
}
