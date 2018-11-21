<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardProperties2 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_properties', function($table)
        {
            $table->string('external_id', 191)->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_properties', function($table)
        {
            $table->integer('external_id')->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
