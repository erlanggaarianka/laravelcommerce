<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Assuming 'tax' column already stores the tax amount
            // Add columns to store the state of tax at the time of transaction
            $table->boolean('is_tax_applied')->default(false)->after('tax'); // Or suitable position
            $table->decimal('tax_rate_snapshot', 5, 2)->default(0.00)->after('is_tax_applied');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['is_tax_applied', 'tax_rate_snapshot']);
        });
    }
};
