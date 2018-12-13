<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1035 extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('users', 'location_id')) {
            return;
        }
        
        Schema::table('users', function($table) {
            $table->integer('location_id')->nullable()->unsigned();
        });
    }

    public function down()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function ($table) {
                $table->dropColumn(['location_id']);
            });
        }
    }
}