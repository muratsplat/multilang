<?php namespace Muratsplat\Multilang\Interfaces;

/**
 * An interface for main models
 * 
 * This interface is for main model, not multi langauge models!
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
interface MainInterface {
    
    /**
     * to get validation rules
     * 
     * @return array
     */
    public function getRules();  
    
    /***
     * If it is true the model has many model includes multi language contents
     * @return bool
     */
    public function isMultilang();
    
    /**
    * to get translation models of main model
    * 
    * use HasMany relationship to access langugae model
    * @return  \Illuminate\Database\Eloquent\Relations\HasMany
    */   
    public function langModels();
        
}
