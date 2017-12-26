<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hours', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('arrangement_id');
            $table->unsignedInteger('consultant_id');
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('task_id');
            $table->date('report_date');
            $table->double('billable_hours');
            $table->double('non_billable_hours')->nullable();
            $table->decimal('rate',15,2)->comment('the billing_rate or pay_rate of its arrangement when this hour was reported');
            $table->unsignedTinyInteger('rate_type')->default(0);
            $table->double('share')->comment('the reporter\'s share of the generated value,share=1 when rate is pay_rate');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('review_state')->default(0)
                ->comment('0=>not-reviewed,1=>review_approved,2=>review_changed,3=>concurred');
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
        Schema::dropIfExists('hours');
    }
}
