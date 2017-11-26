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
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('industry_id');
            $table->unsignedInteger('buz_dev_person_id')->nullable()
                ->comment('client developed by which consultant, if null dev person is the company');
            $table->unsignedInteger('outreferrer_id')->nullable()
                ->comment('client developed by which outside referrer');
            $table->string('name');
            $table->boolean('complex_structure')->nullable();
            $table->boolean('messy_accounting_at_begin')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
