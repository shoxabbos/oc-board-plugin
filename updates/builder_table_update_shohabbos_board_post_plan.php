<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPostPlan extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_post_plan', function($table)
        {
            $table->dateTime('expires')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_post_plan', function($table)
        {
            $table->dropColumn('expires');
        });
    }
}
