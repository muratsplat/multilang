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
class Contents extends Migration {    
    
    public function up() {
        
   
        Schema::create('contents', function($t) {
            
            $t->increments('id');
                       
            $t->boolean('enable')->default(0);            
            $t->boolean('visible')->default(0);
            $t->string('alias', 80)->nullable();
            
            $t->timestamp('created_at')->nullable();
            $t->timestamp('updated_at')->nullable();
            $t->softDeletes();
        });
    }
    
    public function down() {
        
       Schema::drop('contents');
    }
    
}






