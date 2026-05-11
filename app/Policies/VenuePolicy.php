<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Venue $venue): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    public function update(User $user, Venue $venue): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    public function delete(User $user, Venue $venue): bool
    {
        return $user->hasRole('super_admin');
    }
}
