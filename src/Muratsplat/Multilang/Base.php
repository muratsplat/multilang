<?php namespace Muratsplat\Multilang;

use Muratsplat\Multilang\Exceptions\MultiLangConfigNotCorrect;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Class includes common methods for all classes
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
abstract class Base {
  
    /**
     * Laravel Config Object 
     * 
     * @var \Illuminate\Config\Repository 
     */
    protected $config;
    
        /**
         * to get config value via Laravel Config Object
         * 
         * @param string $key
         * @return string
         * @throws Muratsplat\Multilang\Exceptions\MultiLangConfigNotCorrect
         */
        protected function getConfig($key) {

            $name = $this->config->get('multilang::' . $key);

            if(empty($name) || is_null($name)) {

                throw new MultiLangConfigNotCorrect("It looks Multilang configuration is not correct! "
                        . "[$key] key is not found MultiLang's configuration.");
            }

            return $name;   
        }
        
        /**
         * To get hashed Lang Models name as key name for caching
         * 
         * @param \Illuminate\Database\Eloquent\Model $model
         * @return string
         */
        protected function getKeyOfCachedLangModel(Model $model) {
            
            $root  = $this->getConfig('cachePrefix');
                        
            return $root . '/cachedLangModels/'. md5(get_class($model));
        }
        
//        /**
//         * To get hashed Main Models name as key name for caching
//         * 
//         * @param \Illuminate\Database\Eloquent\Model $model
//         * @return string
//         */
//        protected function getKeyOfCachedMainModel(Model $model) {
//            
//            $root  = $this->getConfig('cachePrefix');
//                        
//            return $root . '/cachedLangModels/'. md5(get_class($model));
//        }
}
