<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosBoardPropertyValues2 extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_board_property_values', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('property_id')->unsigned();
            $table->string('key');
            $table->string('value');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_property_values');
    }
}
