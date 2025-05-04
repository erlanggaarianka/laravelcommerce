<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Outlet;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $outlets = Outlet::all();

    Product::factory(50)->create()->each(function ($product) use ($outlets) {
        // Assign product to random outlets
        $product->outlets()->attach(
            $outlets->random(rand(1, $outlets->count()))->pluck('id')->toArray()
        );

        // Create inventory for each assigned outlet
        foreach ($product->outlets as $outlet) {
            Inventory::create([
                'product_id' => $product->id,
                'outlet_id' => $outlet->id,
                'quantity' => rand(0, 100),
                'reorder_level' => 10
            ]);
        }
    });
}
}
