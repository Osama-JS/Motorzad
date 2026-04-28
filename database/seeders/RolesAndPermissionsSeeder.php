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
        // 1. Create Permissions
        $permissions = [
            'view-auctions',
            'place-bids',
            'create-auctions',
            'manage-own-auctions',
            'manage-all-auctions',
            'manage-users',
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
