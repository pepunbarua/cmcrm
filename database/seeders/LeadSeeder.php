<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\Vendor;
use App\Models\User;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $vendor1 = Vendor::first();
        $adminUser = User::where('email', 'admin@checkmate.com')->first() ?? User::first();

        $leads = [
            [
                'vendor_id' => $vendor1?->id, 
                'user_id' => $adminUser->id,
                'client_name' => 'Rashid & Nadia', 
                'client_phone' => '01811111111', 
                'client_email' => 'rashid@example.com',
                'event_type' => 'wedding', 
                'event_date' => '2025-12-15', 
                'budget_range' => '50,000 - 1,00,000',
                'package_interest' => 'Premium Photography + Videography',
                'notes' => 'Looking for full day coverage',
                'status' => 'new',
            ],
            [
                'vendor_id' => $vendor1?->id,
                'user_id' => $adminUser->id,
                'client_name' => 'TechCorp Ltd', 
                'client_phone' => '01822222222', 
                'client_email' => 'hr@techcorp.com', 
                'event_type' => 'corporate', 
                'event_date' => '2025-11-20', 
                'budget_range' => '30,000 - 50,000',
                'package_interest' => 'Corporate Event Photography',
                'notes' => 'Annual conference photography needed',
                'status' => 'contacted',
            ],
            [
                'vendor_id' => $vendor1?->id,
                'user_id' => $adminUser->id,
                'client_name' => 'Anika Rahman', 
                'client_phone' => '01833333333', 
                'event_type' => 'birthday', 
                'event_date' => '2025-11-05', 
                'budget_range' => '15,000 - 25,000',
                'package_interest' => 'Birthday Photography',
                'notes' => 'Kids birthday party',
                'status' => 'follow_up',
            ],
            [
                'vendor_id' => $vendor1?->id,
                'user_id' => $adminUser->id,
                'client_name' => 'Imran & Farhana', 
                'client_phone' => '01844444444',
                'client_email' => 'imran@example.com',
                'event_type' => 'wedding', 
                'event_date' => '2026-01-10', 
                'budget_range' => '80,000 - 1,50,000',
                'package_interest' => 'Deluxe Wedding Package',
                'notes' => 'Destination wedding photography',
                'status' => 'qualified',
            ],
        ];

        foreach ($leads as $lead) {
            Lead::create($lead);
        }

        $this->command->info('Leads created successfully!');
    }
}
