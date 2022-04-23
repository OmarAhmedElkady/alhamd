<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // photo 17 , password 60
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name' , 20)->unique();
            $table->string('email' , 50)->unique();
            $table->char('photo' , 44) ;
            $table->char('password' , 60);
            $table->timestamps();
            // $table->timestamp('email_verified_at')->nullable();
            // $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
