<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardLocations extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_locations', function($table)
        {
            $table->integer('sort')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_locations', function($table)
        {
            $table->dropColumn('sort');
        });
    }
}
