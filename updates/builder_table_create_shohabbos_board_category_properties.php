<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosBoardCategoryProperties extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_board_category_properties', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('property_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('name');
            $table->string('type_filter');
            $table->string('comment');
            $table->boolean('is_visiblefilter');
            $table->string('rules');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_category_properties');
    }
}
