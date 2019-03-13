<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoveReactors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('love_reacter_id');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('love_reactant_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('love_reactant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
