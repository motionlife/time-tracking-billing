<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('contact_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->decimal('standard_rate', 15, 2)->nullable();
            $table->double('standard_percentage')->nullable();
            $table->boolean('isEmployee')->default(0);
            $table->boolean('inactive')->default(0)
                ->comment('default 1=yes');
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
        Schema::dropIfExists('consultants');
    }
}
