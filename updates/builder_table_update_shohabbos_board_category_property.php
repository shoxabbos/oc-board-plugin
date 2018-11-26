<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardCategoryProperty extends Migration
{
    public function up()
    {
        Schema::rename('shohabbos_board_categories_properties', 'shohabbos_board_category_property');
    }
    
    public function down()
    {
        Schema::rename('shohabbos_board_category_property', 'shohabbos_board_categories_properties');
    }
}
