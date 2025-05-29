<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Client Management
            ['name' => 'view_clients', 'display_name' => 'View Clients', 'module' => 'Clients'],
            ['name' => 'create_clients', 'display_name' => 'Create Clients', 'module' => 'Clients'],
            ['name' => 'edit_clients', 'display_name' => 'Edit Clients', 'module' => 'Clients'],
            ['name' => 'delete_clients', 'display_name' => 'Delete Clients', 'module' => 'Clients'],

            // Team Management
            ['name' => 'manage_team_members', 'display_name' => 'Manage Team Members', 'module' => 'Agency'],

            // Feed Management
            ['name' => 'view_feed_sources', 'display_name' => 'View Feed Sources', 'module' => 'Feeds'],
            ['name' => 'manage_feed_sources', 'display_name' => 'Manage Feed Sources', 'module' => 'Feeds'],
            ['name' => 'manage_transformation_rules', 'display_name' => 'Manage Transformation Rules', 'module' => 'Feeds'],
            ['name' => 'manage_export_profiles', 'display_name' => 'Manage Export Profiles', 'module' => 'Feeds'],

            // Add all other permissions from your spec document (v2.1, Section 4.I.4)
            // Example:
            ['name' => 'view_analytics', 'display_name' => 'View Analytics', 'module' => 'Analytics'],
            ['name' => 'manage_segments', 'display_name' => 'Manage Segments', 'module' => 'Intelligence'],
            ['name' => 'manage_reports', 'display_name' => 'Manage Reports', 'module' => 'Reporting'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }
    }
}
