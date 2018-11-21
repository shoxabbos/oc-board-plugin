<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardCategoryProperties extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_category_properties', function($table)
        {
            $table->string('name')->change();
            $table->string('type_filter')->change();
            $table->string('comment')->change();
            $table->string('rules')->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_category_properties', function($table)
        {
            $table->string('name', 191)->change();
            $table->string('type_filter', 191)->change();
            $table->string('comment', 191)->change();
            $table->string('rules', 191)->change();
        });
    }
}
