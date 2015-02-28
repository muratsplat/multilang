<?php namespace Muratsplat\Multilang\Traits;

use Muratsplat\Multilang\Exceptions\LanguageException as Ex;

/**
 * The Trait for Language Model
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
trait LanguageTrait  {    
    
        /**
        * Get Default Language Model
        * 
        * @return type
        * @throws Exception
        */
       public function getDefaultLanguage() {

           $langs = $this->query()->where('default', 1)->get();

           if (count($langs) >1) {

               throw new Ex('There are a lot of default language models!');
           }
           
           return $langs->first();
          
        } 
}
