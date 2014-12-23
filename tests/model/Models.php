<?php namespace Muratsplat\Multilang\Tests\Model;

use Illuminate\Database\Eloquent\Model;
use Muratsplat\Multilang\Interfaces\MainInterface;
use Muratsplat\Multilang\Interfaces\LangInterface;
use Muratsplat\Multilang\Interfaces\AppLanguageInterface;
/**
 *  Simple Models For tests
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */

/**
 * Simple Main Model
 */
class Content extends Model implements MainInterface {
    
    protected $table = "contents";
    
    protected $fillable = array('enable', 'visible');
    
    /**
     * Validation Rules
     * 
     * @var array
     */
    public $rules = array(
            
        'enable'    => 'required',
        'visible'   => 'required',
        
    );
    
    public function getRules() {
        
        return $this->rules;
    }
    
    /**
     * Defining inversed relation to Content
     * 
     * @return Muratsplat\Multilang\Tests\Model\ContentLang
     */
    public function ContentLang() {
        
        return $this->hasMany('Muratsplat\Multilang\Tests\Model\ContentLang');
    }
}

/**
 * ContentLang  will be Content's multi language model.
 */
class ContentLang extends Model implements LangInterface {
    
    protected $table = "contentLangs";
    
    protected $fillable = array('content_id', 'lang_id', 'title', 'content');
    
    /**
     * Validation Rules
     * 
     * @var array
     */
    public $rules = array(
            
        'title'        => 'max:100|RequiredForDefaultLang:Page Title',
        'content'       => 'max:15000',
      
        
    );
    
    public function getRules() {
        
        return $this->rules;
    }
    
    /**
     * Defining inversed relation to Content
     * 
     * @return Muratsplat\Multilang\Tests\Model\Content
     */
    public function Content() {
        
        return $this->belongsTo('Muratsplat\Multilang\Tests\Model\Content');
    }
}

/**
 * App Language Model
 */
class Language extends Model implements AppLanguageInterface {
    
    protected $table = "languages";
    
    protected $fillable = array(
        'name', 
        'name_native', 
        'lang_code', 
        'enable',
        'default',);
    
        
}


