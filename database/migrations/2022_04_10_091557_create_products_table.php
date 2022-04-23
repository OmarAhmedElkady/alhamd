<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('abbr' , 5) ;
            $table->integer('translation_of')->unsigned() ;
            $table->integer('category_id')->unsigned() ;
            $table->string('name' , 100) ;
            $table->char('image' , 44) ;
            $table->double('purchasing_price') ;
            $table->double('pharmacist_price') ;
            $table->double('selling_price') ;
            $table->integer('store') ;
            $table->softDeletes() ;
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
        Schema::dropIfExists('products');
    }
}
