<?php namespace Muratsplat\Multilang\Tests\Model;

use Illuminate\Database\Eloquent\Model;
use Muratsplat\Multilang\Interfaces\MainInterface;
use Muratsplat\Multilang\Interfaces\LangInterface;
use Muratsplat\Multilang\Interfaces\AppLanguageInterface;
use Muratsplat\Multilang\Traits\LangTrait;
use Muratsplat\Multilang\Traits\MainTrait;
use Muratsplat\Multilang\Traits\LanguageTrait;
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
    
    use MainTrait;
    
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
    public function ContentLangs() {
        
        return $this->hasMany('Muratsplat\Multilang\Tests\Model\ContentLang', 'content_id', 'id');
    }    
            
    /**
     * to get Language Models. 
     * use HasMany relationship to access language model
     * 
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */   
     public function langModels() {
         
         return $this->ContentLangs();
     }
}

/**
 * ContentLang  will be Content's multi language model.
 */
class ContentLang extends Model implements LangInterface {
    
    use LangTrait;
    
    protected $table = "contentlangs";
    
    protected $fillable = array('content_id', '__lang_id__', 'title', 'content');

    
    /**
     * Validation Rules
     * 
     * @var array
     */
    public $rules = array(
            
        'title'        => 'max:100|RequiredForDefaultLang:Page Title',
        'content'       => 'max:15000',      
        
    ); 
    
    /**
     * Defining inversed relation to Content
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content() {
        
        return $this->belongsTo('Muratsplat\Multilang\Tests\Model\Content', 'id','content_id');
    }    

    /**
     * reference to main model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainModel() {
             
        return $this->content();
    }
}

/**
 * App Language Model
 */
class Language extends Model implements AppLanguageInterface {
    
    use LanguageTrait;
    
    protected $table = "languages";
    
    protected $fillable = array(
        'name', 
        'name_native', 
        'lang_code', 
        'enable',
        'default',);
    
    
}