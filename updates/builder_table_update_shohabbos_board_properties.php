<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardProperties extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_properties', function($table)
        {
            $table->text('values')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_properties', function($table)
        {
            $table->dropColumn('values');
        });
    }
}
