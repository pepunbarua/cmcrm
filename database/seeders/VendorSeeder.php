<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            ['vendor_name' => 'Grand Palace Hotel', 'vendor_type' => 'hotel', 'city' => 'Dhaka', 'phone' => '01711111111', 'email' => 'info@grandpalace.com', 'status' => 'active'],
            ['vendor_name' => 'Rose Garden Convention', 'vendor_type' => 'convention_hall', 'city' => 'Dhaka', 'phone' => '01722222222', 'email' => 'booking@rosegarden.com', 'status' => 'active'],
            ['vendor_name' => 'Dream Wedding Venue', 'vendor_type' => 'wedding_venue', 'city' => 'Chittagong', 'phone' => '01733333333', 'status' => 'active'],
            ['vendor_name' => 'City Community Center', 'vendor_type' => 'community_center', 'city' => 'Sylhet', 'phone' => '01744444444', 'status' => 'active'],
            ['vendor_name' => 'Royal Banquet Hall', 'vendor_type' => 'wedding_venue', 'city' => 'Dhaka', 'phone' => '01755555555', 'email' => 'events@royal.com', 'status' => 'active'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }

        $this->command->info('Vendors created successfully!');
    }
}
