<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('requester_name');
            $table->string('requester_phone');
            $table->string('requester_fax')->nullable();
            $table->string('scope')->nullable();
            $table->longText('details')->nullable();
            $table->integer('source_id')->unsigned();
            $table->foreign('source_id')
                ->references('id')->on('sources')
                ->onDelete('cascade');

            $table->integer('bid_statuses_id')->unsigned();
            $table->foreign('bid_statuses_id')
                ->references('id')->on('bid_statuses')
                ->onDelete('cascade');

            $table->integer('estimator_id')->unsigned();
            $table->foreign('estimator_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->integer('creator_id')->unsigned();

            $table->foreign('creator_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->integer('assign_id')->unsigned();

            $table->foreign('assign_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->integer('editor_id')->unsigned();

            $table->foreign('editor_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->string('assign_date')->nullable();

            $table->integer('property_id')->unsigned()->nullable();

//            $table->foreign('property_id')
//                ->references('id')->on('properties')
//                ->onDelete('cascade');

            $table->integer('property_phone_id')->unsigned()->nullable();

//            $table->foreign('property_phone_id')
//                ->references('id')->on('property_phones')->onDelete('cascade');

            $table->integer('property_contact_id')->unsigned()->nullable();

//            $table->foreign('property_contact_id')
//                ->references('id')->on('property_contacts')->onDelete('cascade');

            $table->integer('property_contact_phone_id')->unsigned()->nullable();

//            $table->foreign('property_contact_phone_id')
//                ->references('id')->on('property_contact_phones')->onDelete('cascade');

            $table->integer('property_company_id')->unsigned()->nullable();

//            $table->foreign('property_company_id')
//                ->references('id')->on('property_management_companies')->onDelete('cascade');


            $table->integer('property_company_phone_id')->unsigned()->nullable();

//            $table->foreign('property_company_phone_id')
//                ->references('id')->on('property_management_company_phones')->onDelete('cascade');

            $table->integer('property_company_contact_id')->unsigned()->nullable();

//            $table->foreign('property_company_contact_id')
//                ->references('id')->on('property_management_company_contacts')->onDelete('cascade');

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
        Schema::drop('requests');
    }
}
