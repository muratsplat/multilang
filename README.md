#MultiLang For Laravel (in development !!!)
[![Build Status](https://travis-ci.org/muratsplat/multilang.svg?branch=master)](https://travis-ci.org/muratsplat/multilang)

A Laravel extension is make be easy to CRUD ORM proccess for multi languages contents..

##Installing

##How to use in Example


Let's imagine simple Laravel App. We want to have multi language pages.
In this case common way is creating two models firstly.

take a look at:

```sh
 php artisan migrate:make create_pages_table
```
Pages will be our main model's table. Only common attributes should be in the table. It likes this migrate:

```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            Schema::create('pages', function(Blueprint $t) {

                $t->increments('id');
                $t->boolean('enable')->default(0);
                // you can add more for your needs
                // but don't add columns for multi language
                $t->timestamp('created_at')->nullable();
                $t->timestamp('updated_at')->nullable();    
            
            });
         }
         
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
       
            Schema::drop('pages');
        }

}
``` 
Second step is creating other table for just multi language contents..

```sh

php artisan migrate:make create_pageLangs_table

```
Let's edit created migrate again..

```php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageLangsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            
            Schema::create('pageLangs', function(Blueprint $t) {
            
               $t->increments('id');
               $t->integer('page_id')->unsigned();
               // it is required column. Column name can be changed
               // but you have to change the name in the package's configure file.
               // look at ..app/config/package/muratsplat/multilang/config.php
               $t->integer('__lang_id__')->unsigned();
               $t->string('title', 200)->nullable();			
               $t->string('content', 1500)->nullable();
               // you can add more..
               $t->timestamps();

               $t->foreign('page_id')->references('id')->on('pages');

               // This reference is optional. If you want to language model
               // to manage app languages, the refrences is recommended.
               // Having a Language model make be easy to manage language
               $t->foreign('__lang_id__')->references('id')->on('languages');

               });
        }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{       
            Schema::drop('contents');
   
	}

}

``` 
It can be useful to have Language model. For every web app I create an struct to manage languages.
In basic you can create Languge model like this:
```php
use Illuminate\Database\Migrations\Migration;

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
            


            $t->index('lang_code');
            $t->unique(array('lang_code', 'name'));
        });
    }
    
    public function down() {
        
        Schema::drop('languages');
    }

    
    
}
``` 

If you decide to create Language model don't forget make reference as like

```php
// in pageLangs migration class

 $t->foreign('__lang_id__')->references('id')->on('languages');

```

You can create default data for language models by using seeder class

```php

class DatabaseLanguagesSeeder extends Seeder {
    
    // reference : http://www.loc.gov/standards/iso639-2/php/code_list.php
    // reference : http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes 
    public function run() {
               
         Language::create(array('name' => 'Turkish', 'name_native' => 'Türkçe', 
            'lang_code' => 'tr',  'enable' => 1 , 'default' => 1));

        Language::create(array('name' => 'English', 'name_native' => 'English', 
            'lang_code' => 'en', 'enable' => 1));

        Language::create(array('name' => 'German', 'name_native' => 'Deutsch', 
            'lang_code' => 'de', 'enable' => 1));

        Language::create(array('name' => 'French', 'name_native' => 'Français', 
            'lang_code' => 'fr', 'enable' => 1));

        Language::create(array('name' => 'Russian', 'name_native' => 'русский язык', 
            'lang_code' => 'ru', 'enable' => 1));

        Language::create(array('name' => 'Arabic', 'name_native' => 'العربية', 
            'lang_code' => 'ar', 'enable' => 1));        
    }
}
```

###Example Post Data
```php
    /*
     * Simple Post Data
     * 
     */
   $rawPost  = array(
        
        "enable"    => 1,
               
        'title@1'   => "Foo English",
        'content@1' => "Simple example is in English",
        
        'title@2'   => 'Foo Türkçe',
        'content@2' => 'Türkçe bir içerik langur lungur bir yoğurt',
        
        "title@3"   => 'здравствуйте',
        "content@3" => 'Путинхороший человек. Он любит русские , я думаю, россияне любят его.'      
    );
```
the array three different languages contents in same. English, Turkish and Russian
Now let's storage it

```php


```
##Todos



##License

Copyright (C) 2014 Murat ÖDÜNÇ  GPLv3

