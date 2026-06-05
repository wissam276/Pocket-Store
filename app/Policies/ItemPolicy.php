<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    // هل يمكن للمستخدم عرض القائمة؟
    public function viewAny(User $user): bool
    {
        return true; // مسموح للجميع
    }

    // هل يمكن للمستخدم تعديل حالة العنصر؟
    public function update(User $user, Item $item): bool
    {
// السماح بالتعديل في حالتين:
        // 1. إذا كان المستخدم هو صاحب هذا المنتج (البائع)
        // 2. أو إذا كان المستخدم يملك صلاحية "مدير" (Admin)
        return ($item->user_id === $user->name) || ($user->role === 'admin');
    }

    // هل يمكن للمستخدم حذف العنصر؟
    public function delete(User $user, Item $item): bool
    {
        return $user->role === 'admin';
    }
}
