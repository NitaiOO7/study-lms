<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ChannelSubscription;
use App\Models\SubscriptionPlan;
use App\Models\Payment;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = ChannelSubscription::with(['channel.teacher', 'plan'])->latest()->paginate(20);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function plans()
    {
        $plans = SubscriptionPlan::latest()->get();
        return view('admin.subscriptions.plans', compact('plans'));
    }

    public function payments()
    {
        $payments = Payment::with('user')->latest()->paginate(20);
        return view('admin.subscriptions.payments', compact('payments'));
    }
}
