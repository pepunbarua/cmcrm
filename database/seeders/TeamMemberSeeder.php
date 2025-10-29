<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Hash;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        // Create photographers
        $photographer1 = User::create(['name' => 'Karim Ahmed', 'email' => 'karim@checkmate.com', 'password' => Hash::make('password')]);
        $photographer1->assignRole('photographer');
        TeamMember::create(['user_id' => $photographer1->id, 'role_type' => 'photographer', 'skill_level' => 'expert', 'is_default_assigned' => true, 'priority_order' => 1]);

        $photographer2 = User::create(['name' => 'Rahim Khan', 'email' => 'rahim@checkmate.com', 'password' => Hash::make('password')]);
        $photographer2->assignRole('photographer');
        TeamMember::create(['user_id' => $photographer2->id, 'role_type' => 'photographer', 'skill_level' => 'senior', 'priority_order' => 2]);

        // Create videographers
        $videographer1 = User::create(['name' => 'Jamal Hossain', 'email' => 'jamal@checkmate.com', 'password' => Hash::make('password')]);
        $videographer1->assignRole('photographer');
        TeamMember::create(['user_id' => $videographer1->id, 'role_type' => 'videographer', 'skill_level' => 'expert', 'is_default_assigned' => true, 'priority_order' => 1]);

        $videographer2 = User::create(['name' => 'Sohel Rahman', 'email' => 'sohel@checkmate.com', 'password' => Hash::make('password')]);
        $videographer2->assignRole('photographer');
        TeamMember::create(['user_id' => $videographer2->id, 'role_type' => 'videographer', 'skill_level' => 'senior', 'priority_order' => 2]);

        // Sales person
        $sales = User::create(['name' => 'Sumaiya Akter', 'email' => 'sales@checkmate.com', 'password' => Hash::make('password')]);
        $sales->assignRole('manager');

        $this->command->info('Team members created successfully!');
    }
}
