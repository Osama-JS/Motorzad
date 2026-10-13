<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // All Granular System Permissions
        $permissions = [
            // Core & Platform
            'access-admin-panel',
            'view-auctions',
            'place-bids',
            'create-auctions',
            'manage-own-auctions',

            // Admin Modules
            'manage-all-auctions',
            'manage-vehicles',
            'manage-bids',
            'manage-users',
            'manage-roles',
            'manage-seller-requests',

            // Financial & Wallets
            'manage-wallets',
            'manage-withdrawals',
            'manage-deposits',
            'manage-bank-accounts',

            // Content & Support
            'manage-notifications',
            'send-notifications',
            'manage-contacts',
            'manage-pages',
            'manage-news',
            'manage-faqs',
            'manage-marketing',

            // Technical Support
            'manage-tickets',
            'reply-tickets',

            // System & Technical
            'manage-settings',
            'manage-backups',
            'view-system-health',
            'manage-system-health',
        ];

        foreach ($permissions as $permissionName) {
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
        // Keep permissions or remove new ones
    }
};
