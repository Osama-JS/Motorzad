<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Granular System Permissions
        $permissions = [
            // Core & Platform
            'access-admin-panel',
            'view-auctions',
            'place-bids',
            'create-auctions',
            'manage-own-auctions',

            // Admin Modules
            'manage-all-auctions',
            'view-admin-auctions',
            'edit-auctions',
            'pause-auctions',
            'force-end-auctions',
            'block-auction-bidders',
            'delete-auctions',

            'manage-vehicles',
            'approve-reject-vehicles',

            'manage-bids',
            'void-bids',

            'manage-users',
            'view-users',
            'edit-users',
            'delete-users',
            'verify-users-kyc',

            'manage-roles',
            'manage-seller-requests',

            // Financial & Wallets
            'manage-wallets',
            'view-wallets',
            'edit-user-balance',
            'manage-debt-ceilings',
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

        foreach ($permissions as $p) {
            \Spatie\Permission\Models\Permission::findOrCreate($p);
        }

        // 2. Create Roles
        $adminRole = \Spatie\Permission\Models\Role::findOrCreate('admin');
        $sellerRole = \Spatie\Permission\Models\Role::findOrCreate('seller');
        $bidderRole = \Spatie\Permission\Models\Role::findOrCreate('bidder');

        // 3. Assign Permissions to Roles
        $adminRole->syncPermissions(\Spatie\Permission\Models\Permission::all());
        
        $sellerRole->syncPermissions(['view-auctions', 'place-bids', 'create-auctions', 'manage-own-auctions']);

        $bidderRole->syncPermissions(['view-auctions', 'place-bids']);
    }
}
