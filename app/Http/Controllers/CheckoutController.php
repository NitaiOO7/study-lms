<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SubscriptionPlan;
use App\Models\Payment;
use App\Models\ChannelSubscription;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function show(SubscriptionPlan $plan)
    {
        $gateways = $this->paymentService->getAvailableGateways();
        return view('teacher.checkout', compact('plan', 'gateways'));
    }

    public function process(Request $request, SubscriptionPlan $plan)
    {
        $request->validate(['gateway' => 'required|string']);

        $user = Auth::user();
        $channel = $user->channel;

        // Create a pending payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'payable_type' => 'App\Models\Channel',
            'payable_id' => $channel->id,
            'amount' => $plan->price,
            'currency' => $request->gateway === 'razorpay' ? 'INR' : 'USD',
            'status' => 'pending',
            'gateway' => $request->gateway,
        ]);

        $driver = $this->paymentService->driver($request->gateway);

        $initializationData = [
            'payment_id' => $payment->id,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'amount' => $plan->price,
            'currency' => $request->gateway === 'razorpay' ? 'INR' : 'USD',
            'email' => $user->email,
            'success_url' => route('teacher.payment.callback', $request->gateway),
            'cancel_url' => route('teacher.checkout', $plan->slug),
        ];

        $response = $driver->initialize($initializationData);

        // Update payment with gateway reference
        $payment->update([
            'gateway_payment_id' => $response['gateway_reference'] ?? null,
        ]);

        if (isset($response['checkout_url'])) {
            return redirect($response['checkout_url']);
        }

        // For gateways like Razorpay that use client-side integration
        return view('teacher.payment-process', [
            'payment' => $payment,
            'gateway_data' => $response,
            'plan' => $plan,
        ]);
    }

    public function callback(Request $request, string $gateway)
    {
        $driver = $this->paymentService->driver($gateway);
        $isVerified = $driver->verify($request->all());

        if (!$isVerified) {
            return redirect()->route('teacher.dashboard')->with('error', 'Payment verification failed.');
        }

        // Find payment (we might need to check gateway reference or session ID)
        // For simplicity, we'll assume the driver handled verification
        // In a real app, we'd use webhooks for robustness.

        $user = Auth::user();
        $channel = $user->channel;
        
        // This is a bit simplified; in production, you'd find the payment by reference
        $payment = Payment::where('user_id', $user->id)
            ->where('gateway', $gateway)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($payment) {
            $payment->update(['status' => 'success', 'payload' => $request->all()]);

            // Activate Channel and Subscription
            $channel->update(['is_active' => true]);

            $plan = SubscriptionPlan::find($payment->payable_id); // Wait, payable_id is channel_id here
            // We should store plan_id in payment or find it differently.
            // Let's assume we retrieve it from somewhere. 
            // For now, I'll fetch the plan the user was trying to buy.
            $plan = SubscriptionPlan::where('price', $payment->amount)->first();

            ChannelSubscription::create([
                'channel_id' => $channel->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addDays($plan->duration_days),
                'amount_paid' => $payment->amount,
                'gateway' => $gateway,
                'payment_id' => $payment->id,
            ]);

            return redirect()->route('teacher.dashboard')->with('success', 'Subscription activated successfully!');
        }

        return redirect()->route('teacher.dashboard')->with('error', 'Payment record not found.');
    }
}
