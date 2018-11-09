<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_folders', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('client_id')->unsigned()->references('id')->on('api_keys')->index();
            $table->integer('user_id')->unsigned()->references('id')->on('users')->index();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->integer('parent')->default(0);
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create('media_storage', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('client_id')->unsigned()->references('id')->on('api_keys')->index();
            $table->integer('user_id')->unsigned()->references('id')->on('users')->index();
            $table->integer('folder_id')->default(0);
            $table->string('mime_type', 120);
            $table->string('file_name');
            $table->integer('size');
            $table->string('path', 255);

            $table->timestamps();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_folders');
        Schema::dropIfExists('media_storage');
    }
}
