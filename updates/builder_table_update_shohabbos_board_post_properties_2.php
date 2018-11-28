<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPostProperties2 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_post_properties', function($table)
        {
            $table->integer('property_id');
            $table->dropColumn('key');
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_post_properties', function($table)
        {
            $table->dropColumn('property_id');
            $table->string('key', 191);
        });
    }
}
