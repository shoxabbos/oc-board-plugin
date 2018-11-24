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
            $table->integer('user_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->string('title');
            $table->string('slug');
            $table->text('content');
            $table->string('contact_name');
            $table->string('phone');
            $table->string('email');
            $table->integer('views')->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_posts');
    }
}
