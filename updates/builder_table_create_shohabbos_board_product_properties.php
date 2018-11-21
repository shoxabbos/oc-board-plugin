<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosBoardProductProperties extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_board_product_properties', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('category_id');
            $table->integer('product_id');
            $table->string('key');
            $table->string('value');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_product_properties');
    }
}
