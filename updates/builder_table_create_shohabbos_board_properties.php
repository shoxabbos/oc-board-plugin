<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosBoardProperties extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_board_properties', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('label');
            $table->string('comment')->nullable();
            $table->string('type');
            $table->text('settings')->nullable();
            $table->integer('category_id');
            $table->string('filter_type')->nullable();
            $table->boolean('show_as_filter')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_properties');
    }
}
