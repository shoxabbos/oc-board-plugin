<?php namespace Shohabbos\Board\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1036 extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('users', 'phone')) {
            return;
        }
        
        Schema::table('users', function($table) {
            $table->string('phone')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function ($table) {
                $table->dropColumn(['phone']);
            });
        }
    }
}