<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosBoardCategories extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_board_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('slug');
            $table->integer('sort');
            $table->integer('parent_id')->unsigned();
            $table->string('seo_desc')->nullable();
            $table->string('seo_keys')->nullable();
            $table->string('seo_title')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_categories');
    }
}
