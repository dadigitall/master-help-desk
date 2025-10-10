<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
        ]);

        // Create a default admin user
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@helpdesk.com',
        ]);
        $admin->assignRole('Administrator');

        // Create a test employee
        $employee = User::factory()->create([
            'name' => 'Test Employee',
            'email' => 'employee@helpdesk.com',
        ]);
        $employee->assignRole('Employee');

        // Create a test customer
        $customer = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@helpdesk.com',
        ]);
        $customer->assignRole('Customer');
    }
}
