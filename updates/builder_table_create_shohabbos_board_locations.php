<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosBoardLocations extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_board_locations', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->integer('parent_id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_locations');
    }
}
