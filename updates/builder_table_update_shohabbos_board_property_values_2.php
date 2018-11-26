<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPropertyValues2 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_property_values', function($table)
        {
            $table->integer('property_id')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_property_values', function($table)
        {
            $table->integer('property_id')->nullable(false)->change();
        });
    }
}
