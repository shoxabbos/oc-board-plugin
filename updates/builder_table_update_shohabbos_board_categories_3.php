<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardCategories3 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_categories', function($table)
        {
            $table->integer('sort')->default(0)->change();
            $table->integer('parent_id')->unsigned()->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_categories', function($table)
        {
            $table->integer('sort')->default(null)->change();
            $table->integer('parent_id')->unsigned(false)->change();
        });
    }
}
