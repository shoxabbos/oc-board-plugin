<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardReviews extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_reviews', function($table)
        {
            $table->integer('from_user_id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_reviews', function($table)
        {
            $table->dropColumn('from_user_id');
        });
    }
}
