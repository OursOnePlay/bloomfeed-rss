<?php

namespace App\Policies;

use App\Models\Notifier;
use App\Models\User;

class NotifierPolicy
{
    public function update(User $user, Notifier $notifier): bool
    {
        return $user->id === $notifier->user_id;
    }

    public function delete(User $user, Notifier $notifier): bool
    {
        return $user->id === $notifier->user_id;
    }
}
