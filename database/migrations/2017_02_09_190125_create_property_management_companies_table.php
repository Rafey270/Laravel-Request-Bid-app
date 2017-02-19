<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyManagementCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('management_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('management_company')->nullable();
            $table->string('management_company_street')->nullable();
            $table->string('management_company_city')->nullable();
            $table->string('management_company_state')->nullable();
            $table->string('management_company_zip')->nullable();

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
        Schema::drop('management_companies');
    }
}
