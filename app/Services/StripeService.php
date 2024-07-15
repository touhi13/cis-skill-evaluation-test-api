<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\StripeCustomer\StripeCustomerRepositoryInterface;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;

class StripeService
{
    protected $stripe;
    protected $stripeCustomerRepository;

    public function __construct(StripeCustomerRepositoryInterface $stripeCustomerRepository)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->stripe                   = new Stripe();
        $this->stripeCustomerRepository = $stripeCustomerRepository;
    }

    public function createCheckoutSession($priceId, User $user)
    {
        $customer = $this->createCustomer($user);
// dd(env('APP_URL') . '/success');
        $checkoutSession = Session::create([
            'customer'             => $customer->id,
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price'    => $priceId,
                'quantity' => 1,
            ]],
            'mode'                 => 'subscription',
            'success_url'          => env('APP_URL') . '/success',
            'cancel_url'           => env('APP_URL') . '/cancel',
        ]);

        return $checkoutSession;
    }

    protected function createCustomer(User $user)
    {
        // dd($user);
        if ($user->stripe_id) {
            return Customer::retrieve($user->stripe_id);
        }

        $customer = Customer::create([
            'email' => $user->email,
            'name'  => $user->name, // Adjust as per your user data
        ]);

        $this->stripeCustomerRepository->createOrUpdate(
            $user,
            $customer->id,
        );

        return $customer;
    }
}
