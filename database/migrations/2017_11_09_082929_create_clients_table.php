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
            $table->string('name');
            $table->unsignedInteger('contact_id')->nullable();
            $table->unsignedInteger('industry_id');
            //client developed by which consultant
            $table->unsignedInteger('business_dev_person_id');
            //client developed by which outside referrer
            $table->unsignedInteger('out_referrer_id')->nullable();
            //below 1 column should belong to engagements table
            //$table->date('engagement_start_date')->nullable();
            $table->unsignedInteger('revenue_id')->nullable();
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
