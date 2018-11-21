<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPropertyValues extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_property_values', function($table)
        {
            $table->string('value')->change();
            $table->string('label')->nullable()->change();
            $table->string('slug')->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_property_values', function($table)
        {
            $table->string('value', 191)->change();
            $table->string('label', 191)->nullable(false)->change();
            $table->string('slug', 191)->change();
        });
    }
}
