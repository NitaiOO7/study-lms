@extends('layouts.app')

@section('content')
<div class="container animate-in" style="padding-top: 60px; max-width: 800px; margin: 0 auto;">
    <div class="card p-5">
        <div class="text-center mb-5">
            <h1 class="page-title">Complete Your Subscription</h1>
            <p class="text-secondary">You are subscribing to the <strong>{{ $plan->name }}</strong> for <strong>${{ $plan->price }}</strong></p>
        </div>

        <div class="row" style="display: flex; gap: 30px;">
            <!-- Plan Summary -->
            <div style="flex: 1; border-right: 1px solid var(--border); padding-right: 30px;">
                <h3 style="margin-bottom: 20px;">Plan Summary</h3>
                <div style="background: var(--bg-secondary); padding: 20px; border-radius: 12px;">
                    <h4 style="margin: 0;">{{ $plan->name }}</h4>
                    <p style="font-size: 0.9rem; color: var(--text-secondary); margin: 10px 0;">{{ $plan->description }}</p>
                    <hr style="border: 0; border-top: 1px solid var(--border); margin: 15px 0;">
                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem;">
                        <span>Total:</span>
                        <span style="color: var(--primary);">${{ $plan->price }}</span>
                    </div>
                </div>
                <div class="mt-4">
                    <h4 style="margin-bottom: 10px;">Features included:</h4>
                    <ul style="font-size: 0.9rem; color: var(--text-secondary);">
                        @foreach($plan->features as $feature)
                        <li style="margin-bottom: 5px;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 8px;"></i> {{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Payment Methods -->
            <div style="flex: 1;">
                <h3 style="margin-bottom: 20px;">Select Payment Method</h3>
                <form action="{{ route('teacher.checkout.process', $plan->slug) }}" method="POST">
                    @csrf
                    <div class="gateway-list" style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach($gateways as $id => $gateway)
                        <label class="gateway-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid var(--border); border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                            <input type="radio" name="gateway" value="{{ $id }}" {{ $loop->first ? 'checked' : '' }} style="margin-right: 15px;">
                            <div style="flex: 1; font-weight: bold; font-size: 1.1rem;">{{ $gateway['name'] }}</div>
                            <!-- Gateway Logo placeholders -->
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                <i class="fas fa-credit-card"></i> Card / Wallet
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg mt-5" style="padding: 15px;">
                        <i class="fas fa-lock" style="margin-right: 10px;"></i> Pay Now
                    </button>
                    
                    <p class="text-center mt-3" style="font-size: 0.8rem; color: var(--text-secondary);">
                        Secure encrypted payment powered by our trusted partners.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .gateway-option:hover {
        border-color: var(--primary);
        background: rgba(var(--primary-rgb), 0.05);
    }
    input[type="radio"]:checked + div + div {
        color: var(--primary);
    }
    .gateway-option input[type="radio"]:checked ~ * {
        color: var(--primary);
    }
    .gateway-option:has(input:checked) {
        border-color: var(--primary);
        background: rgba(var(--primary-rgb), 0.1);
    }
</style>
@endsection
