<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardProperties3 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_properties', function($table)
        {
            $table->dropColumn('category_id');
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_properties', function($table)
        {
            $table->integer('category_id');
        });
    }
}
