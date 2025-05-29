<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Agency Admin gets all permissions (or a defined set)
        $agencyAdminRole = Role::where('name', 'agency_admin')->first();
        if ($agencyAdminRole) {
            $allPermissionIds = Permission::pluck('id');
            $agencyAdminRole->permissions()->sync($allPermissionIds);
        }

        // Agency Manager permissions
        $agencyManagerRole = Role::where('name', 'agency_manager')->first();
        if ($agencyManagerRole) {
            $managerPermissionNames = [
                'view_clients', 'create_clients', 'edit_clients', // Assuming they can manage clients
                'view_feed_sources', 'manage_feed_sources',
                'manage_transformation_rules',
                'manage_export_profiles',
                'view_analytics',
                'manage_segments',
                'manage_reports',
            ];
            $managerPermissionIds = Permission::whereIn('name', $managerPermissionNames)->pluck('id');
            $agencyManagerRole->permissions()->sync($managerPermissionIds);
        }

        // Agency Analyst permissions
        $agencyAnalystRole = Role::where('name', 'agency_analyst')->first();
        if ($agencyAnalystRole) {
            $analystPermissionNames = [
                'view_clients',
                'view_feed_sources',
                'view_analytics', // Typically view-only for analytics
                // Add specific view permissions for other modules as needed
            ];
            $analystPermissionIds = Permission::whereIn('name', $analystPermissionNames)->pluck('id');
            $agencyAnalystRole->permissions()->sync($analystPermissionIds);
        }
    }
}
