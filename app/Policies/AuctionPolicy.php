<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AuctionPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Auction $auction): bool
    {
        // View logic for own auction
        return $user->id === $auction->created_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Auction $auction): bool
    {
        // Can only update if they own it AND it's draft or scheduled.
        // Wait, the user specifically agreed to pending or draft for auctions.
        // Auction status can be draft, scheduled, live, completed, cancelled.
        // Let's stick to draft.
        return $user->id === $auction->created_by && in_array($auction->status, ['draft']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Auction $auction): bool
    {
        return $user->id === $auction->created_by && in_array($auction->status, ['draft', 'cancelled']);
    }
}
