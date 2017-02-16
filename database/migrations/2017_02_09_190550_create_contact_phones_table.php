<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_phones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('phone_type_id')->unsigned();
            $table->foreign('phone_type_id')
                ->references('id')->on('phone_types')
                ->onDelete('cascade');
            $table->string('area_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_ext')->nullable();

            $table->integer('contact_id')->unsigned();

            $table->foreign('contact_id')
                ->references('id')->on('contacts')
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
        Schema::drop('contact_phones');
    }
}
