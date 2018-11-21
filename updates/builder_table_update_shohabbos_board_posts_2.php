<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPosts2 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_posts', function($table)
        {
            $table->string('slug');
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_posts', function($table)
        {
            $table->dropColumn('slug');
        });
    }
}
