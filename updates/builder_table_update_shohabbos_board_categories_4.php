<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardCategories4 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_categories', function($table)
        {
            $table->dropColumn('filters');
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_categories', function($table)
        {
            $table->text('filters')->nullable();
        });
    }
}
