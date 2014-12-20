<?php namespace Muratsplat\Multilang;

use Illuminate\Database\Eloquent\Model;
use Muratsplat\Multilang\Picker;
use Illuminate\Config\Repository as Config;
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
class MultiLang {
    
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
    
    


        public function __construct(Picker $picker, Model $model, Config $config) {
            
            $this->picker = $picker;
            
            $this->mainModel = $model;
            
            $this->config= $config;
            
            
    
            
        }
        
        
}
