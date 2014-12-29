MultiLang For Laravel (in development !!!)
=============
[![Build Status](https://travis-ci.org/muratsplat/multilang.svg?branch=master)](https://travis-ci.org/muratsplat/multilang)

A Laravel extension is make be easy to CRUD ORM proccess for multi languages contents..

Installing
----------

How to use in Example
---------------------

Let's imagine simple Laravel App. We want to have multi language pages.
In this case common way is creating two models firstly.

take a look at:

```sh
 php artisan migrate:make create_pages_table
```
Pages  will be our main model's table. Only common attributes should be in thetable. It likes this migrate:
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

                $t->incements('id');
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


Todos
-----


License
--------
Copyright (C) 2014 Murat ÖDÜNÇ  GPLv3

