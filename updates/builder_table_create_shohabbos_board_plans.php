<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateShohabbosBoardPlans extends Migration
{
    public function up()
    {
        Schema::create('shohabbos_board_plans', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->text('description');
            $table->integer('days');
            $table->integer('amount');
            $table->string('type');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_plans');
    }
}
