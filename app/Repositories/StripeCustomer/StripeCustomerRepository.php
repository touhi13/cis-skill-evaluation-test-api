<?php

namespace App\Repositories\StripeCustomer;

use App\Models\User;

class StripeCustomerRepository implements StripeCustomerRepositoryInterface
{
    public function createOrUpdate(User $user, $stripeId)
    {
        $user->stripe_id = $stripeId;
        $user->save();

        return $user;
    }
}
