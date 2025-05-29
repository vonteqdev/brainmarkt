<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class, // Assign permissions after roles and permissions exist
            // You would create these for initial setup:
            // AgencySeeder::class,
            // UserSeeder::class, // To create a default agency admin user
        ]);
    }
}
