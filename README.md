#MultiLang Package For Laravel 4.2 (in development !!!)

[![Build Status](https://travis-ci.org/muratsplat/multilang.svg?branch=master)](https://travis-ci.org/muratsplat/multilang) [![Latest Stable Version](https://poser.pugx.org/muratsplat/multilang/v/stable.svg)](https://packagist.org/packages/muratsplat/multilang) [![Total Downloads](https://poser.pugx.org/muratsplat/multilang/downloads.svg)](https://packagist.org/packages/muratsplat/multilang) [![Latest Unstable Version](https://poser.pugx.org/muratsplat/multilang/v/unstable.svg)](https://packagist.org/packages/muratsplat/multilang) [![License](https://poser.pugx.org/muratsplat/multilang/license.svg)](https://packagist.org/packages/muratsplat/multilang)

A Laravel extension is make be easy to CRUD ORM proccess for multi languages contents.

##Requiretments

- PHP 5.4+ or HHVM lastest
- Composer
- Laravel 4.2.x


##Installing 

Add `muratsplat/multilang` as a requirement to composer.json
```javascript
    "require" : {
        "muratsplat\multilang": "dev-master"
    },

```
Update your packages with `composer update` or install with `composer install`.

You can also add the package using `composer require muratsplat/multilang` on your terminal.

This package only supports Composer to instaling.

###Configuration

Firstly you should add the package's service provider to your laravel app
```php
 'Muratsplat\Multilang\MultilangServiceProvider',
```

Also you can add the package's alias 

```php
 'MultiLang' =>'Muratsplat\Multilang\Facades\MultiLang',

```
Finally install to the package's configuration via artisan like this

in your project's main folder:
```bash

 php artisan config:publish muratsplat/multilang

```
Now you can change the package's configuration by editing `..app/config/packages/muratsplat/multilang/config.php` 


##How to use in Example

Let's imagine simple Laravel App. We want to have multi language pages.
In this case common way is creating two models firstly. For them let's create tables via Laravel Migration.

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
Tables are ready. It is the turn of creating models.

Main model will be Page model in example. Main model must be implement 'Muratsplat\Multilang\Interfaces\MainInterface' and it must be used 'Muratsplat\Multilang\Traits\MainTrait'.

Page Model
```php
use Muratsplat\Multilang\Interfaces\MainInterface;
use Muratsplat\Multilang\Traits\MainTrait;

class Page extends \Eloquent implements MainInterface {

    use MainTrait;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pages';


    protected $fillable = array('enable');
    
        /**
         * Validation Rules
         * 
         * @var array
         */
        public $rules = array(

            'enable'    => 'required',           
        );

        /**
         * Defining inversed relation to Content
         * 
         * @return PageLang
         */
        public function PageLangs() {

            return $this->hasMany('PageLang', 'page_id', 'id');
        }    

        /**
        * to get Language Models. 
        * use HasMany relationship to access language model
        * 
        * @return  \Illuminate\Database\Eloquent\Relations\HasMany
        */   
        public function langModels() {

            return $this->PageLangs();
        }
    }
``` 
For pageLangs table we are creating PageLang Model. PageLang must be implement 'Muratsplat\Multilang\Interfaces\LangInterface' and must be used 'Muratsplat\Multilang\Traits\LangTrait' trait in it. That's like this:

```php
use Muratsplat\Multilang\Interfaces\LangInterface;
use Muratsplat\Multilang\Traits\LangTrait;

class PageLang extends \Eloquent implements LangInterface {

    use LangTrait;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pageLangs';


    protected $fillable = array('content_id', 'lang_id', 'title', 'content'); 
    
     /**
     * Validation Rules
     * 
     * @var array
     */
    public $rules = array(
            
        'title'        => 'max:100|RequiredForDefaultLang:@,1,Title',
        'content'      => 'max:15000|RequiredForDefaultLang:@,1,Content',      
        
    ); 
      
        /**
         * Defining inversed relation to Content
         * 
         * @return PageLang
         */
        public function Page() {

            return $this->belongsTo('Page', 'id', 'page_id');
        }    

        /**
        * to get Language Models. 
        * use HasMany relationship to access language model
        * 
        * @return  \Illuminate\Database\Eloquent\Relations\HasMany
        */   
        public function mainModel() {

            return $this->Page();
        }
    }
```
Multilang gets with new rule. 'RequiredForDefaultLang' rule validates elements for default language id. If default language is Turkish, and if Turkish element is empty, validation is failed.

RequiredForDefaultLang accepts tree parameters. First of these prefix for picking elements up and second parameter is default language id and also last parameter is replacer in error message.

You can add a message for the rule by editing '..app/lang/en/validation.php'.

example:
```php
"required_for_default_lang" => ":explain, is required for default language."
```

Validation rules can be in models. But it is not required. You can add rules in your controller. It is recommended that rules is in models. This make keep clean on your controller.

### Dynamic Form Example

You can create form as down on view layer.

```php
  //..page.blade.php
    
    {{Form::open(['action'=>['PageController@store']])}}

        {{Form::text('enable', 'Aktif')}}
    
        @foreach($langs as $v)
        
            {{Form::text("title@$v->id", 'Bir başlık giriniz..')}}
            {{Form::textarea("content@$v->id")}}
            
       @endforeach 
       
    {{Form::close()}}
```

This form sends post data to a controller...

```php
    
    $rawPost  = array(

            "enable"    => 1,

            'title@1'   => "Foo English",
            'content@1' => "Simple example of content in English",

            'title@2'   => 'Foo Türkçe',
            'content@2' => 'Türkçe bir içerik langur lungur bir yoğurt',

            "title@3"   => 'здравствуйте',
            "content@3" => 'Путинхороший человек. Он любит русские , я думаю, россияне любят его.'      
        );

 ```

You can use MultiLang on your controller..
 
```php

	$rules = ['enable'    => 'required']; 
    // if the rule is in models, it is overwrote on same rule in models. 
    // So one of in model is updated by overwriting
	
    // Rules parameter is optional. 
    // If you have been defined rules in Page model, rules parameter is not need.

    if(MultiLang::create($rawPost, new Page(), $rules)) {
        // it is in success
    } else {
		$instace = MultiLang::getInstance();

		Redirect::route('panel.create')->withErrors($instace)->withInput();
	}
```
let's create a wrapper to access two models at one point
```php
    $wantedLangId = 3;            
    $defaultLangId = 1; // if the value is null or empty, returns PageLang models by Language id
    $wrapper = MultiLang::makeWarapper(Page::find(1), $wantedLangId,$defaultLangId);

    echo $wrapper->title; // returns: "здравствуйте"
    echo $wrapper->content // returns: "Путинхороший человек. Он любит русские , я думаю, россияне любят его."

```
##Todos



##License

Copyright (C) 2014 Murat ÖDÜNÇ  GPLv3

