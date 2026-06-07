<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function update(User $user, Item $item): bool
    {

        return ($item->user_id === $user->name) || ($user->role === 'admin');
    }

    public function delete(User $user, Item $item): bool
    {
        return $user->role === 'admin';
    }
}
