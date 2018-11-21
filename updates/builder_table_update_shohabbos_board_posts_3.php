<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPosts3 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_posts', function($table)
        {
            $table->integer('location_id')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_posts', function($table)
        {
            $table->integer('location_id')->nullable(false)->change();
        });
    }
}
