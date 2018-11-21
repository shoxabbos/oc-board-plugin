<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardCategories3 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_categories', function($table)
        {
            $table->integer('sort')->default(0);
            $table->string('slug')->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_categories', function($table)
        {
            $table->dropColumn('sort');
            $table->string('slug', 191)->change();
        });
    }
}
