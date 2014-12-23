<?php //namespace Muratsplat\Multilang\Tests\Migrate;

use Illuminate\Database\Migrations\Migration;

/**
 *  Migrations For tests
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */
class ContentLangs extends Migration {
    
    
    public function up() {
        
        Schema::create('ContentLangs', function($t) {
            
            $t->increments('id');
            $t->integer('content_id')->unsigned();
            $t->integer('_lang_id')->unsigned();
            $t->string('title', 100)->nullable();			
            $t->string('content', 15000)->nullable();
            $t->timestamps();
            
            $t->foreign('content_id')->references('id')->on('contens');
            $t->foreign('_lang_id')->references('id')->on('languages');
              
        });
    }
    
    public function down() {
        
        Schema::drop('contentLangs');
    }

    
}

