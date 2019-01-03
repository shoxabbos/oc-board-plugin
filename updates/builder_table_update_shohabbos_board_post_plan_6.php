<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPostPlan6 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_post_plan', function($table)
        {
            $table->renameColumn('expires', 'expires_at');
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_post_plan', function($table)
        {
            $table->renameColumn('expires_at', 'expires');
        });
    }
}
