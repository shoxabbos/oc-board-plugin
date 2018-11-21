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
            $table->string('type');
            $table->string('desc')->nullable();
            $table->string('measure')->nullable();
            $table->string('code')->nullable();
            $table->integer('external_id')->nullable();
            $table->boolean('is_translatable')->default(0);
            $table->string('tab_name')->nullable();
            $table->boolean('is_active')->default(1);
            $table->text('settings')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('shohabbos_board_properties');
    }
}
