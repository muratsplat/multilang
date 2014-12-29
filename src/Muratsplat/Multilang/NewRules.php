<?php namespace  Muratsplat\Multilang;

use Symfony\Component\Translation\TranslatorInterface;
use Illuminate\Validation\Validator as validInLaravel;

/**
 * The class includes new vallidation rules for Multilang
 *
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class NewRules  extends validInLaravel  {   

    /**
    * Create a new Validator instance.
    *
    * @param  \Symfony\Component\Translation\TranslatorInterface  $translator
    * @param  array  $data
    * @param  array  $rules
    * @param  array  $messages
    * @param  array  $customAttributes
    * @return void
    */
    public function __construct(TranslatorInterface $translator, array $data, array $rules, array $messages = array(), array $customAttributes = array())
    {
        parent::__construct($translator, $data, $rules, $messages, $customAttributes);
        
        $this->implicitRules[] = 'RequiredForDefaultLang';        
    }
    
    /**
     * It checks default language fields. If these are empty,
     * return false.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @return boolean
     */
    protected function validateRequiredForDefaultLang($attribute, $value, $parameters) {

        $this->requireParameterCount(2, $parameters, 'RequiredForDefaultLang');
        // getting lang Id by looking the prefix such as 'title@1'
        $id = substr($attribute, strpos($attribute, $parameters[0]) +1, strlen($attribute));
        
        if ((integer) $parameters[1] === (integer) $id) {

            return  $this->validateRequired($attribute, $value);
        }

        return true;
    }
    
    /**
     * A Replece For RequiredForDefaultLang
     * 
     * @param type $message
     * @param type $attribute
     * @param type $rule
     * @param type $parameters
     * @return mixed
     */
    protected function replaceRequiredForDefaultLang($message, $attribute, $rule, $parameters) {
        
        return str_replace(':explain', $parameters[0], $message);
    }  
}

