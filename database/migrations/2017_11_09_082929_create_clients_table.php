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
            $table->unsignedTinyInteger('industry_id');
            $table->string('business_dev_person')->nullable();
            $table->string('out_referrer')->nullable();
            $table->date('engagement_start')->nullable();
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
