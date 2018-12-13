<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardLocations2 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_locations', function($table)
        {
            $table->integer('parent_id')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_locations', function($table)
        {
            $table->integer('parent_id')->nullable(false)->change();
        });
    }
}
