<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('property_name');

            $table->integer('property_type_id')->unsigned();
            $table->foreign('property_type_id')
                ->references('id')->on('property_types')
                ->onDelete('cascade');

            $table->string('property_street')->nullable();
            $table->string('property_city')->nullable();
            $table->string('property_state')->nullable();
            $table->string('property_zip')->nullable();
            $table->string('property_country')->nullable();

            $table->integer('mail_to_type_id')->unsigned();
            $table->foreign('mail_to_type_id')
                ->references('id')->on('mail_to_types')
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
        Schema::drop('properties');
    }
}
