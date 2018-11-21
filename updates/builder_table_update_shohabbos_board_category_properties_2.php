<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardCategoryProperties2 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_category_properties', function($table)
        {
            $table->string('name')->change();
            $table->string('type_filter')->change();
            $table->string('comment')->nullable()->change();
            $table->boolean('is_visiblefilter')->default(0)->change();
            $table->string('rules')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_category_properties', function($table)
        {
            $table->string('name', 191)->change();
            $table->string('type_filter', 191)->change();
            $table->string('comment', 191)->nullable(false)->change();
            $table->boolean('is_visiblefilter')->default(null)->change();
            $table->string('rules', 191)->nullable(false)->change();
        });
    }
}
