<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_type')->nullable();

            $table->string('description')->nullable();

            $table->string('file_name')->nullable();

            $table->string('file_change_name')->nullable();

            $table->integer('request_id')->unsigned();

            $table->foreign('request_id')
                ->references('id')->on('requests')
                ->onDelete('cascade');

            $table->integer('creator_id')->unsigned();

            $table->foreign('creator_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

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
        Schema::drop('request_files');
    }
}
