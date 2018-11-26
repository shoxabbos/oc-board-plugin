<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosBoardCategoriesProperties extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_board_categories_properties', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('property_id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_categories_properties');
    }
}
