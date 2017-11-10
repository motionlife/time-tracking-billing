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
            $table->float('buz_dev_share')->default(0);
            //used to indicate whether the engagement has closed or sth
            $table->unsignedTinyInteger('status')->default(0);
            //indicate how the client pay,0=engagement fixed,1=/hour,2=/15-day,3=/month,4=/year...
            $table->unsignedTinyInteger('paying_cycle')->default(0);
            //billing amount to client every term/cycle
            $table->decimal('billing_amount',15,2)->default(0);
            $table->decimal('billing_total',15,2)->default(0);
            $table->decimal('operating_profit',15,2)->default(0);
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
