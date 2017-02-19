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
            $table->string('requester_name')->nullable();
            $table->string('requester_phone')->nullable();
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


            $table->integer('approved_id')->unsigned()->nullable();

//            $table->foreign('approved_id')
//                ->references('id')->on('users')
//                ->onDelete('cascade');

            $table->string('assign_date')->nullable();

            $table->string('billing_date')->nullable();

            $table->string('paid_date')->nullable();

            $table->integer('property_id')->unsigned()->nullable();

            $table->integer('bid_template_id')->unsigned()->nullable();

            $table->integer('bid_type_id')->unsigned()->nullable();

            $table->integer('approved_flag')->unsigned()->nullable();

            $table->integer('request_bid_job_id')->unsigned()->nullable();

            $table->boolean('archive_bid')->nullable()->default(0);

            $table->string('due_date')->nullable();

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
