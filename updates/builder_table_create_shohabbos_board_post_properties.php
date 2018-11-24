<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosBoardPostProperties extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_board_post_properties', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('post_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->string('key');
            $table->string('value');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_post_properties');
    }
}
