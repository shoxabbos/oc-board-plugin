<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteShohabbosBoardPropertyValues extends Migration
{
    public function up()
    {
        Schema::dropIfExists('shohabbos_board_property_values');
    }
    
    public function down()
    {
        Schema::create('shohabbos_board_property_values', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('label', 191);
            $table->string('value', 191);
            $table->integer('property_id')->unsigned();
        });
    }
}
