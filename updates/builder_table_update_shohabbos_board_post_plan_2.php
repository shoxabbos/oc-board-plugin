<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPostPlan2 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_post_plan', function($table)
        {
            $table->dateTime('expires')->nullable(false)->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_post_plan', function($table)
        {
            $table->dateTime('expires')->nullable()->change();
        });
    }
}
