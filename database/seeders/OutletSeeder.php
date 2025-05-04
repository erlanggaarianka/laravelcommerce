<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    Outlet::create([
        'name' => 'pgLAng',
        'address' => '123 Main Street',
        'phone' => '123-456-7890'
    ]);

    Outlet::create([
        'name' => 'TDE',
        'address' => '456 Center Ave',
        'phone' => '987-654-3210'
    ]);
}
}
