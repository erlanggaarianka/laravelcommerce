<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->string('action')->after('reason')->default('adjust');
        });
    }

    public function down()
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropColumn('action');
        });
    }
};
