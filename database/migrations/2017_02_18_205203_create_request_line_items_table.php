<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_line_items', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('line_item_description')->nullable();
            $table->longText('lump_sum')->nullable();
            $table->string('order')->nullable();
            $table->string('option_flag')->nullable();

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
        Schema::drop('request_line_items');
    }
}
