<?php namespace  Muratsplat\Multilang;


/**
 * The class includes new vallidation rule and replacer for Multilang
 *
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class NewRule  {
    
    /**
     * It checks default language fields. If these are empty,
     * return false.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @return boolean
     */
    public function validateRequiredForDefaultLang($attribute, $value, $parameters) {

        $this->requireParameterCount(2, $parameters, 'RequiredForDefaultLang');
        // getting lang Id by looking the prefix such as 'title@1'
        $id = substr($attribute, strpos($attribute, $parameters[0]) +1, strlen($attribute));
        
        if ((integer) $parameters[1] === (integer) $id) {

            return  $this->validateRequired($attribute, $value);
        }

        return true;
    }
    
    /**
     * A Replecer For RequiredForDefaultLang
     * 
     * @return Callback
     */
    public function replaceRequiredForDefaultLang() {
        
        return function($message, $attribute, $rule, $parameters) {
               
            return isset($parameters[2])? str_replace(':explain', $parameters[2], $message) : $message;
        };      
    }  
}

