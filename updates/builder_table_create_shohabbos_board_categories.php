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
            $table->string('desc');
            $table->string('seo_title')->nullable();
            $table->string('seo_desc')->nullable();
            $table->string('seo_keys')->nullable();
            $table->integer('parent_id')->nullable();
            $table->boolean('is_active')->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_categories');
    }
}
