<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardReviews2 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_reviews', function($table)
        {
            $table->renameColumn('from_user_id', 'author_id');
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_reviews', function($table)
        {
            $table->renameColumn('author_id', 'from_user_id');
        });
    }
}
