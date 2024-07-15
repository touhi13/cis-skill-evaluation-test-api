<?php

namespace App\Repositories\StripeCustomer;

use App\Models\User;

interface StripeCustomerRepositoryInterface
{
    public function createOrUpdate(User $user, $stripeId);
}
