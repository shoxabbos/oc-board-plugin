<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPostProperties extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_post_properties', function($table)
        {
            $table->integer('post_id')->nullable()->change();
            $table->integer('category_id')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_post_properties', function($table)
        {
            $table->integer('post_id')->nullable(false)->change();
            $table->integer('category_id')->nullable(false)->change();
        });
    }
}
