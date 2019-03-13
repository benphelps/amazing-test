<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('love_reaction_types')->insert(
            [
                'name' => 'Like',
                'weight' => 1
            ]
        );
        DB::table('love_reaction_types')->insert(
            [
                'name' => 'Dislike',
                'weight' => -1
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
