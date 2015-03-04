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
class Images extends Migration {    
    
    public function up() {        
   
        Schema::create('images', function($t) {
            
            $t->increments('id');
            
            $t->integer('content_id')->unsigned();
            
            $t->string('file_name');
            
            $t->timestamps();
            
            $t->foreign('content_id')->references('id')->on('contents');
        });
    }
    
    public function down() {
        
       Schema::drop('images');
    }
    
}






