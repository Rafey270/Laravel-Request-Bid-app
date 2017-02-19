<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyManagementCompanyPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('management_company_phones', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('management_company_id')->unsigned();

            $table->foreign('management_company_id')
                ->references('id')->on('management_companies')
                ->onDelete('cascade');

            $table->integer('phone_id')->unsigned();

            $table->foreign('phone_id')
                ->references('id')->on('phones')
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
        Schema::drop('management_company_phones');
    }
}
