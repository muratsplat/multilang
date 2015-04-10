<?php

/**
 *  Multilang Package's Configuration
 * 
 * @package Multilang
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
return array(

        /*
         * Example: '@', ':', '+'
         * 
         * The chracter is used by picking multi language elements up in post array
         */
	'prefix'            => '@',
    
        /**
         * Default Language ID
         */
        'defaultLangId'     => 1,
    
        /*
         * Reserved attribute name for multi language models.
         * 
         * The key only must be used by Multilang Packages..
         */    
        'reservedAttribute' => '__lang_id__',
        
        /**
         * System Language Model
         * 
         * This model will be used for detecting default language
         */
        'languageModel'     => 'Language',
         
        /**
         * To set a prefix for storing cache driver on your app.
         * 
         * The prefix will be root for this extension.
         */
        'cachePrefix'       => 'multilang',
        
        /**
         * Remember time for caching
         * 
         * The value must be integer and positive number.
         * Time duration is minute.
         */
        'rememberTime'      => 10,
    
    );