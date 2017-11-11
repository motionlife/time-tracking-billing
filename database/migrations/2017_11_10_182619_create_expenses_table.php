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
            $table->date('report_date');
            $table->unsignedInteger('engagement_id');
            $table->unsignedInteger('consultant_id');
            $table->boolean('company_paid');//Indicate whether expense already paid by New Life CFO
            $table->decimal('hotel',15,2);
            $table->decimal('flight',15,2);
            $table->decimal('meal',15,2);
            $table->decimal('office_supply',15,2);
            $table->decimal('car_rental',15,2);
            //mileage_cost = mileage * 0.535
            $table->decimal('mileage_cost',15,2);
            $table->decimal('other',15,2);
            $table->unsignedInteger('receipt_id');
            $table->text('description');
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
