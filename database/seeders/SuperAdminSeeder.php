<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_role = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );
        
        $client_role = Role::firstOrCreate(
            ['name' => 'client'],
            ['guard_name' => 'web']
        );

        // Create Super Admin user
        $admin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'username' => 'super_admin',
                'password' => Hash::make('super_admin_123'), // Change this to a secure password
            ]
        );
        $client = Client::firstOrCreate(
            ['email' => 'usman.centosquare@gmail.com'],
            [
                'name' => 'Test Customer 1',
                'username' => 'test_customer_1',
                'password' => Hash::make('12345678'), // Change this to a secure password
            ]
        );

        // Assign role to user
        $admin->assignRole($admin_role);
        $client->assignRole($client_role);
    }
}
