<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardLocations4 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_locations', function($table)
        {
            $table->integer('nest_left');
            $table->integer('nest_right');
            $table->integer('nest_depth')->nullable();
            $table->dropColumn('sort');
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_locations', function($table)
        {
            $table->dropColumn('nest_left');
            $table->dropColumn('nest_right');
            $table->dropColumn('nest_depth');
            $table->integer('sort')->default(0);
        });
    }
}
