<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardCategoryProperties3 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_category_properties', function($table)
        {
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_category_properties', function($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
