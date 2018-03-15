<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCloserToEngagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('engagements', function (Blueprint $table) {
            //add closer feature to each engagement
            $table->unsignedInteger('closer_id')->default(0)->nullable()
                ->comment('The consultant who also serves as the engagement closer');
            $table->double('closer_share')->default(0)->nullable();
            $table->date('closer_from')->nullable();
            $table->date('closer_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('engagements', function (Blueprint $table) {
            //
            $table->dropColumn('closer_id');
            $table->dropColumn('closer_share');
            $table->dropColumn('closer_from');
            $table->dropColumn('closer_end');
        });
    }
}
