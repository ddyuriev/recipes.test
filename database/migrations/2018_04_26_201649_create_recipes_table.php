<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('login')->unique();
            $table->string('password');
            $table->string('name');
            $table->string('address');
            $table->string('image');
            $table->string('api_token')->unique();
            $table->timestamp('token_created_at')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
        });

        Schema::create('recipes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('doctor_name');
            $table->string('preparation_name');
            $table->text('description')->nullable();
            $table->string('image');
            $table->date('date');
            $table->timestamps();
        });

        Schema::table('role_user', function ($table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('role_user', function ($table) {
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipes');
    }
}
