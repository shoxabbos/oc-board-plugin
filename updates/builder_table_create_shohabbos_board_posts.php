<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosBoardPosts extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_board_posts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title');
            $table->text('content');
            $table->integer('user_id');
            $table->integer('category_id');
            $table->integer('location_id');
            $table->string('phone');
            $table->string('email');
            $table->string('contact_name');
            $table->string('status');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_posts');
    }
}
