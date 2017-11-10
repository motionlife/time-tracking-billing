<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEngagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engagements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('client_id');
            $table->date('start_date');
            $table->date('close_date')->nullable();
            $table->unsignedInteger('leader_id');
            $table->unsignedInteger('collaboration_id');
            $table->float('buz_dev_share')->nullable();
            //used to indicate whether the engagement has closed or sth
            $table->unsignedTinyInteger('status')->nullable();
            //client paid by 1. hourly or 2. month-fixed or 3. engagement-fixed
            $table->unsignedTinyInteger('billing_type');
            //the total amount of money should bill the client
            $table->decimal('billing_total',15,2)->nullable();
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
        Schema::dropIfExists('engagements');
    }
}
