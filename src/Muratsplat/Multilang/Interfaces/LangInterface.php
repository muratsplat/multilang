<?php namespace Muratsplat\Multilang\Interfaces;

/**
 * An interface for main models
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
interface LangInterface {
    
    /**
     * to get validation rules
     * 
     * @return array
     */
    public function getRules();    
    
    /**
     * Simple way to accass to main model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainModel();
    

}
