<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('name');
            $table->unsignedInteger('industry_id');
            //client developed by which consultant, if null dev person is the company
            $table->unsignedInteger('buz_dev_person_id')->nullable();
            //client developed by which outside referrer
            $table->unsignedInteger('outreferrer_id')->nullable();
            //below 1 column should belong to engagements table
            //$table->date('engagement_start_date')->nullable();
            $table->boolean('complex_structure')->nullable();
            $table->boolean('messy_accounting_at_begin')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
