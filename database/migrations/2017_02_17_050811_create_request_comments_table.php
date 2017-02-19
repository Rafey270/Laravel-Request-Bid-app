<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('comment')->nullable();

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
        Schema::drop('request_comments');
    }
}
