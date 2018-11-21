<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardCategoryProperties4 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_category_properties', function($table)
        {
            $table->renameColumn('rules', 'validation');
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_category_properties', function($table)
        {
            $table->renameColumn('validation', 'rules');
        });
    }
}
