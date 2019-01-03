<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1056 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_post_plan', function($table) {
            $table->unique(['post_id', 'plan_id']);
        });
    }

    public function down()
    {
        Schema::table('shohabbos_board_post_plan', function ($table) {
            $table->dropUnique('shohabbos_board_post_plan_post_id_plan_id_unique');
        });
    }
}