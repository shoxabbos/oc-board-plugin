<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPropertyValues extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_property_values', function($table)
        {
            $table->renameColumn('key', 'label');
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_property_values', function($table)
        {
            $table->renameColumn('label', 'key');
        });
    }
}
