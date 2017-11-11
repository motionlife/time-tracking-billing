<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->increments('id');
            //user_id could be either client or consultant
            $table->unsignedInteger('user_id');
            //0=>billing;1=>payroll_hours;2=>payroll_expense;3=>payroll_biz_dev
            $table->unsignedTinyInteger('type');
            $table->dateTime('start_from');
            $table->dateTime('end_at');
            //0=>not read; 1=>read_not_approved;2=>read_approved;3=>not_concur!...
            $table->unsignedTinyInteger('status')->default(0);
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
        Schema::dropIfExists('approvals');
    }
}
