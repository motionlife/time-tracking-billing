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
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('leader_id');
            $table->string('name');
            $table->date('start_date');
            $table->date('close_date')->nullable();
            $table->double('buz_dev_share')->default(0);
            //indicate whether the engagement has closed or sth
            $table->unsignedTinyInteger('status')->default(1)
                ->comment('0=>open,1=>closed');
            //indicate how the client gonna pay, ie. paying cycle type
            $table->unsignedTinyInteger('paying_cycle')->default(0)
                ->comment('0=/hourly,1=/15-day,2=/month,3=/year,4=engagement fixed,..');
            //billing amount to client every term/cycle
            $table->decimal('cycle_billing', 15, 2)->default(0)
                ->comment('not given when paying_cycle is hourly');
            //$table->decimal('total_billing', 15, 2)->default(0);
            //$table->decimal('operating_profit', 15, 2)->default(0)->comment('=total_billing - payrolls');
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
