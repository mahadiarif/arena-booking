<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Customer $customer): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Customer $customer): bool
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Staff: only customers they created
        return (int) $customer->created_by === $user->id;
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }
}
