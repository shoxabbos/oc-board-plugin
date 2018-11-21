<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPostProperties extends Migration
{
    public function up()
    {
        Schema::rename('shohabbos_board_product_properties', 'shohabbos_board_post_properties');
    }
    
    public function down()
    {
        Schema::rename('shohabbos_board_post_properties', 'shohabbos_board_product_properties');
    }
}
