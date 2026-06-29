<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\Response;

class VehiclePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        // User can view if they submitted it, or if it's approved and part of an active auction (handled in auction).
        // For the vehicle endpoints (e.g. /my-vehicles), they should only view their own.
        return $user->id === $vehicle->submitted_by;
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
    public function update(User $user, Vehicle $vehicle): bool
    {
        // Can only update if they own it AND it's pending or rejected
        return $user->id === $vehicle->submitted_by && in_array($vehicle->status, ['pending', 'rejected']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        // Can only delete if they own it AND it's pending or rejected
        return $user->id === $vehicle->submitted_by && in_array($vehicle->status, ['pending', 'rejected']);
    }
}
