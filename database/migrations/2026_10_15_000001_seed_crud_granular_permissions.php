<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations to seed granular CRUD system permissions.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // All Granular CRUD Permissions
        $granularPermissions = [
            // Users Granular
            'view-users',
            'edit-users',
            'delete-users',
            'verify-users-kyc',

            // Auctions Granular
            'view-admin-auctions',
            'edit-auctions',
            'pause-auctions',
            'force-end-auctions',
            'block-auction-bidders',
            'delete-auctions',

            // Vehicles & Bids Granular
            'approve-reject-vehicles',
            'void-bids',

            // Financial & Wallets Granular
            'view-wallets',
            'edit-user-balance',
            'manage-debt-ceilings',
        ];

        foreach ($granularPermissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        // Ensure admin role has all permissions synced
        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->syncPermissions(Permission::all());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep permissions or remove
    }
};
