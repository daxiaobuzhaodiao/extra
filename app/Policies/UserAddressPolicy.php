<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\UserAddress;

class UserAddressPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public isAuthorOf(User $currentUser, UserAddress $userAddress)
    {
        return $currentUser->id === $userAddress->user_id;
    }
}
