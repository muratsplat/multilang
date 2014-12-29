<?php namespace Muratsplat\Multilang;

use Muratsplat\Multilang\Exceptions\MultiLangConfigNotCorrect;

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
     * @var /Illuminate\Config\Repository 
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

            if(empty($name)) {

                throw new MultiLangConfigNotCorrect('It looks Multilang configuration is not correct!');
            }

            return $name;   
        }       
}