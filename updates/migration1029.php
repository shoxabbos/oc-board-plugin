<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1029 extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('users', 'balance')) {
            return;
        }
        
        Schema::table('users', function($table) {
            $table->integer('balance')->nullable()->unsigned();
        });
    }

    public function down()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function ($table) {
                $table->dropColumn(['balance']);
            });
        }
    }
}