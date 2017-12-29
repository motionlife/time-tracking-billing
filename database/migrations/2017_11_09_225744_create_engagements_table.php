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
            $table->unsignedTinyInteger('status')->default(0)
                ->comment('0=>pending,1=>active,2=>closed');
            //indicate Client Billed Type: Hourly; Monthly Retainer; Fixed Fee Project;
            $table->unsignedTinyInteger('paying_cycle')->default(0)
                ->comment('0=/hourly,1=/monthly retainer,2= Fixed Fee Project,..');
            //billing amount to client every term/cycle
            $table->decimal('cycle_billing', 15, 2)->default(0)
                ->comment('effective when paying_cycle is monthly');
            $table->unsignedTinyInteger('billing_day')->default(0)->nullable()
                ->comment('day of each month when billing the client');
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
        Schema::dropIfExists('engagements');
    }
}
