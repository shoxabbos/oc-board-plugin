<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPostPlan3 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_post_plan', function($table)
        {
            $table->dateTime('expires')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_post_plan', function($table)
        {
            $table->dateTime('expires')->nullable(false)->change();
        });
    }
}
