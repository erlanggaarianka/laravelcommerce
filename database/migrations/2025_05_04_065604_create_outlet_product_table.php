<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('outlet_product', function (Blueprint $table) {
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['outlet_id', 'product_id']); // Composite primary key
            $table->timestamps(); // Optional, if you want to track when relationships were created
        });
    }

    public function down()
    {
        Schema::dropIfExists('outlet_product');
    }
};
