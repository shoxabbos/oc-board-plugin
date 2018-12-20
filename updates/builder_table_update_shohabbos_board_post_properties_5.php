<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPostProperties5 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_post_properties', function($table)
        {
            $table->string('value', 191)->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_post_properties', function($table)
        {
            $table->string('value', 191)->nullable(false)->change();
        });
    }
}
