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
            $table->boolean('company_paid')->default(false);//Indicate whether expense already paid by New Life CFO
            $table->decimal('hotel',15,2)->default(0);
            $table->decimal('flight',15,2)->default(0);
            $table->decimal('meal',15,2)->default(0);
            $table->decimal('office_supply',15,2)->default(0);
            $table->decimal('car_rental',15,2)->default(0);
            //mileage_cost = mileage * 0.535
            $table->decimal('mileage_cost',15,2)->default(0);
            $table->decimal('other',15,2)->default(0);
            $table->unsignedInteger('receipt_id')->nullable();
            $table->text('description')->nullable();
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
