<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('arrangement_id');
            $table->date('report_date');
            $table->boolean('company_paid')->default(false)
            ->comment('whether the expense had already paid by New Life CFO');
            $table->decimal('hotel',15,2)->default(0)->nullable();
            $table->decimal('flight',15,2)->default(0)->nullable();
            $table->decimal('meal',15,2)->default(0)->nullable();
            $table->decimal('office_supply',15,2)->default(0)->nullable();
            $table->decimal('car_rental',15,2)->default(0)->nullable();
            //mileage_cost = mileage * 0.535
            $table->decimal('mileage_cost',15,2)->default(0)->nullable();
            $table->decimal('other',15,2)->default(0)->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('review_state')->default(0)
                ->comment('0=>not-reviewed,1=>review_approved,2=>review_changed,3=>concurred');
            $table->text('feedback')->nullable();
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
        Schema::dropIfExists('expenses');
    }
}
