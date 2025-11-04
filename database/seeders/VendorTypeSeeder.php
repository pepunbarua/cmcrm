<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VendorType;

class VendorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendorTypes = [
            [
                'name' => 'Wedding Venue',
                'icon' => 'fa-rings-wedding',
                'is_active' => true,
                'order' => 1
            ],
            [
                'name' => 'Convention Hall',
                'icon' => 'fa-building',
                'is_active' => true,
                'order' => 2
            ],
            [
                'name' => 'Community Center',
                'icon' => 'fa-users',
                'is_active' => true,
                'order' => 3
            ],
            [
                'name' => 'Hotel',
                'icon' => 'fa-hotel',
                'is_active' => true,
                'order' => 4
            ],
            [
                'name' => 'Restaurant',
                'icon' => 'fa-utensils',
                'is_active' => true,
                'order' => 5
            ],
            [
                'name' => 'Resort',
                'icon' => 'fa-umbrella-beach',
                'is_active' => true,
                'order' => 6
            ],
            [
                'name' => 'Other',
                'icon' => 'fa-ellipsis',
                'is_active' => true,
                'order' => 99
            ]
        ];

        foreach ($vendorTypes as $type) {
            VendorType::create($type);
        }
    }
}
