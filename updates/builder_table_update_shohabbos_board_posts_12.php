<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateShohabbosBoardPosts12 extends Migration
{
    public function up()
    {
        Schema::table('shohabbos_board_posts', function($table)
        {
            $table->boolean('published')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->dropColumn('status');
        });
    }
    
    public function down()
    {
        Schema::table('shohabbos_board_posts', function($table)
        {
            $table->dropColumn('published');
            $table->dropColumn('published_at');
            $table->string('status', 191);
        });
    }
}
