<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Dashboard
            'view dashboard',
            
            // Vendors
            'view vendors', 'create vendors', 'edit vendors', 'delete vendors',
            
            // Leads
            'view leads', 'create leads', 'edit leads', 'delete leads', 'assign leads',
            
            // Follow-ups
            'view follow-ups', 'create follow-ups', 'edit follow-ups',
            
            // Orders
            'view orders', 'create orders', 'edit orders', 'delete orders',
            
            // Events
            'view events', 'create events', 'edit events', 'delete events', 'assign team',
            
            // Team Members
            'view team members', 'create team members', 'edit team members', 'delete team members',
            
            // Payments
            'view payments', 'create payments', 'edit payments',
            
            // Deliverables
            'view deliverables', 'upload deliverables', 'edit deliverables',
            
            // Reports
            'view reports',
            
            // Settings
            'manage users', 'manage roles', 'view activity log',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $photographerRole = Role::firstOrCreate(['name' => 'photographer']);

        // Assign all permissions to admin
        $adminRole->syncPermissions(Permission::all());

        // Assign specific permissions to manager
        $managerRole->syncPermissions([
            'view dashboard',
            'view vendors', 'create vendors', 'edit vendors',
            'view leads', 'create leads', 'edit leads', 'assign leads',
            'view follow-ups', 'create follow-ups', 'edit follow-ups',
            'view orders', 'create orders', 'edit orders',
            'view events', 'create events', 'edit events', 'assign team',
            'view payments', 'create payments',
            'view reports',
        ]);

        // Assign specific permissions to photographer
        $photographerRole->syncPermissions([
            'view dashboard',
            'view events',
            'view deliverables', 'upload deliverables',
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@checkmate.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole('admin');

        // Log activity
        activity()
            ->causedBy($admin)
            ->log('Admin user created via seeder');

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@checkmate.com');
        $this->command->info('Password: password');
    }
}
