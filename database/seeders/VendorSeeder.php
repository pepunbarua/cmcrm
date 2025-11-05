<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\VendorType;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        // Get vendor types
        $hotelType = VendorType::where('name', 'Hotel')->first();
        $conventionType = VendorType::where('name', 'Convention Hall')->first();
        $weddingVenueType = VendorType::where('name', 'Wedding Venue')->first();
        $communityType = VendorType::where('name', 'Community Center')->first();

        $vendors = [
            [
                'vendor_name' => 'Grand Palace Hotel',
                'vendor_type_id' => $hotelType?->id,
                'city' => 'Dhaka',
                'phone' => '01711111111',
                'email' => 'info@grandpalace.com',
                'status' => 'active'
            ],
            [
                'vendor_name' => 'Rose Garden Convention',
                'vendor_type_id' => $conventionType?->id,
                'city' => 'Dhaka',
                'phone' => '01722222222',
                'email' => 'booking@rosegarden.com',
                'status' => 'active'
            ],
            [
                'vendor_name' => 'Dream Wedding Venue',
                'vendor_type_id' => $weddingVenueType?->id,
                'city' => 'Chittagong',
                'phone' => '01733333333',
                'status' => 'active'
            ],
            [
                'vendor_name' => 'City Community Center',
                'vendor_type_id' => $communityType?->id,
                'city' => 'Sylhet',
                'phone' => '01744444444',
                'status' => 'active'
            ],
            [
                'vendor_name' => 'Royal Banquet Hall',
                'vendor_type_id' => $weddingVenueType?->id,
                'city' => 'Dhaka',
                'phone' => '01755555555',
                'email' => 'events@royal.com',
                'status' => 'active'
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }

        $this->command->info('Vendors created successfully!');
    }
}
