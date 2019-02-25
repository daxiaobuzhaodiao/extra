<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Order;

class OrderPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        
    }

    public function isOwnerOf(User $user, Order $order)
    {
        return $user->id === $order->user_id;
    }
}
