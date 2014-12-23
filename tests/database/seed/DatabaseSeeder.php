<?php

use Illuminate\Database\Seeder;
/**
 *  Seeds For tests
 * 
 * @author Murat Ödünç <murat.asya@gmail.com>
 * @copyright (c) 2015, Murat Ödünç
 * @link https://github.com/muratsplat/multilang Project Page
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3 
 */

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
        Eloquent::unguard();
        
        $this->call('DatabaseLanguagesSeeder');
	}

}

use Muratsplat\Multilang\Tests\Model\Language;

class DatabaseLanguagesSeeder extends Seeder {
    
    // reference : http://www.loc.gov/standards/iso639-2/php/code_list.php
    // reference : http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes 
    public function run() {
        
        DB::table('langs');

         Language::create(array('name' => 'Türkçe', 'name_native' => 'Türkçe', 
            'lang_code' => 'tr',  'enable' => 1 , 'default' => 1));

        Language::create(array('name' => 'İngilizce', 'name_native' => 'English', 
            'lang_code' => 'en', 'enable' => 1));

        Language::create(array('name' => 'Almanca', 'name_native' => 'Deutsch', 
            'lang_code' => 'de', 'enable' => 1));

        Language::create(array('name' => 'Fransızca', 'name_native' => 'Français', 
            'lang_code' => 'fr', 'enable' => 1));

        Language::create(array('name' => 'Rusça', 'name_native' => 'русский язык', 
            'lang_code' => 'ru', 'enable' => 1));

        Language::create(array('name' => 'Arapça', 'name_native' => 'العربية', 
            'lang_code' => 'ar', 'enable' => 1));        
    }
}