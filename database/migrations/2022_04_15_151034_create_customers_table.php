<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('abbr' , 5) ;
            $table->integer('translation_of')->unsigned() ;
            $table->enum('client_permissions' , ['pharmaceutical' , 'special_customer' , 'customer']) ;
            $table->string('name' , 40) ;
            $table->string('phone' , 11)->nullable() ;
            $table->text('title')->nullable() ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
