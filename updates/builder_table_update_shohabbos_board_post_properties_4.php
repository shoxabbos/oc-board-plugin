<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPostProperties4 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_post_properties', function($table)
        {
            $table->integer('post_id')->unsigned()->change();
            $table->integer('category_id')->unsigned()->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_post_properties', function($table)
        {
            $table->integer('post_id')->unsigned(false)->change();
            $table->integer('category_id')->unsigned(false)->change();
        });
    }
}
