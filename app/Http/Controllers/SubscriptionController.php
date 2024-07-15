<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Services\StripeService;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Exception\ApiErrorException;

class SubscriptionController extends Controller
{
    use ApiResponseTrait;

    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createCheckoutSession(Request $request): JsonResponse
    {
        try {
            $user            = $request->user(); // Assuming you have authenticated user
            $checkoutSession = $this->stripeService->createCheckoutSession('price_1PcTIzBl487OItYzZfEz80fB', $user);

            return response()->json([
                'checkout_url' => $checkoutSession->url,
            ]);
        } catch (ApiErrorException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        Log::info('Webhook received:', $request->all());

        // Verify the webhook signature
        $payload   = json_decode($request->getContent(), true); // Convert JSON string to array
        $sigHeader = $request->server('HTTP_STRIPE_SIGNATURE');
        $secret    = config('services.stripe.webhook_secret');
        // dd($secret);
        try {
            // Construct event from the payload
            $event = Event::constructFrom($payload, $sigHeader, $secret);
            // dd($event->type);
            switch ($event->type) {
                case 'invoice.payment_succeeded':
                    $invoice    = $event->data->object;
                    $customerId = $invoice->customer;
                    $amount     = $invoice->amount_paid / 100;
                    $currency   = $invoice->currency;
                    $paymentId  = $invoice->payment_intent;
                    $invoicePdf = $invoice->invoice_pdf;

                    // dd($invoicePdf);

                    $user = User::where('stripe_id', $customerId)->first();

                    // dd($user);
                    if ($user) {
                        $user->is_active          = true;
                        $user->activation_expires = now()->addMonth();
                        $user->save();

                        Payment::create([
                            'user_id'           => $user->id,
                            'stripe_payment_id' => $paymentId,
                            'amount'            => $amount,
                            'currency'          => $currency,
                            'paid_at'           => now(),
                            'invoice'           => $invoicePdf,
                            'status'            => 'succeeded',

                        ]);
                    }
                    break;
                case 'invoice.payment_failed':
                    $invoice    = $event->data->object;
                    $customerId = $invoice->customer;
                    $paymentId  = $invoice->payment_intent;

                    $user = User::where('stripe_id', $customerId)->first();
                    if ($user) {
                        $user->is_active = false;
                        $user->save();

                        Payment::create([
                            'user_id'           => $user->id,
                            'stripe_payment_id' => $paymentId,
                            'amount'            => 0,
                            'currency'          => $invoice->currency,
                            'status'            => 'failed',
                        ]);
                    }
                    break;
                default:
                    // Handle other event types
                    break;
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Webhook signature verification failed'], 400);
        }
    }

    public function monthlyPaymentReport(Request $request)
    {
        $user = $request->user();
        try {
            $currentMonth = Carbon::now()->startOfMonth();

            $payments = Payment::where('user_id', $user->id)
                // ->whereBetween('paid_at', [$currentMonth, $currentMonth->copy()->endOfMonth()])
                ->latest('id')
                ->paginate(10);

            // Calculate total payments for the month
            $totalPayments = $payments->sum('amount');

            $data = [
                'payments'      => $payments,
                'totalPayments' => $totalPayments,
            ];

            return $this->ResponseSuccess($data, null, 'Monthly Payment Report', 200);
        } catch (\Exception $e) {
            return $this->ResponseError($e->getMessage(), null, 'Error retrieving monthly payment report', 500);
        }
    }
}
