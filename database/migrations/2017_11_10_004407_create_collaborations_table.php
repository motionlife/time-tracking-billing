<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollaborationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collaborations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('engagement_id');
            $table->unsignedInteger('consultant_id');
            //the billing rate the consultant charge the client per hour
            $table->decimal('billing_rate',15,2);
            //indicate the percentage of share that company should get
            $table->float('firm_share');
            //identify the position of this consultant
            $table->unsignedInteger('position_id');
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
        Schema::dropIfExists('collaborations');
    }
}
