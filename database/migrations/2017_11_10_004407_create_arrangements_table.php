<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArrangementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arrangements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('engagement_id');
            $table->unsignedInteger('consultant_id');
            $table->unsignedInteger('position_id')->comment('job position of the consultant');
            $table->decimal('billing_rate', 15, 2)
                ->comment('used as pay_rate when client\'s pay_cycle is not hourly');
            $table->double('firm_share')->comment('the percentage of share that company should get');
            $table->tinyInteger('status')->default(0)->comment('0:normal arrangement;1. detached from engagement');
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
        Schema::dropIfExists('arrangements');
    }
}
