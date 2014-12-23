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
class languages extends Migration {
    
        
    public function up() {
        
        Schema::create('languages', function($t) {
          

            $t->increments('id');
            $t->string('lang_code', 10);
            $t->string('name', 50);
            $t->string('name_native', 50);
            $t->tinyInteger('enable' )->default(0);
            $t->boolean('default')->default(false);
            $t->timestamps();
            $t->softDeletes();


            $t->index('lang_code');
            $t->unique(array('lang_code', 'name'));
            //$table->engine = 'InnoDB';

		

        });
    }
    
    public function down() {
        
        Schema::drop('languages');
    }

    
    
}


