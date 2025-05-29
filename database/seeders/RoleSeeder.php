<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Using DB facade for more control if IDs are not auto-incrementing or for consistency
        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'name' => 'agency_admin', 'display_name' => 'Agency Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'agency_manager', 'display_name' => 'Agency Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'agency_analyst', 'display_name' => 'Agency Analyst', 'created_at' => now(), 'updated_at' => now()],
            // Add Platform Super Admin if needed, potentially managed outside typical agency roles
            // ['id' => 100, 'name' => 'platform_super_admin', 'display_name' => 'Platform Super Administrator', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
