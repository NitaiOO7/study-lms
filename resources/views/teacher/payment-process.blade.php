@extends('layouts.app')

@section('content')
<div class="container animate-in" style="padding-top: 60px; max-width: 600px; margin: 0 auto;">
    <div class="card p-5 text-center">
        <h2 class="mb-4">Processing Payment</h2>
        <p class="text-secondary mb-4">Please complete the payment in the popup to activate your <strong>{{ $plan->name }}</strong> subscription.</p>
        
        @if($payment->gateway === 'razorpay')
            <button id="rzp-button1" class="btn btn-primary btn-lg mt-3" style="padding: 15px 30px;">
                <i class="fas fa-external-link-alt" style="margin-right: 10px;"></i> Open Payment Window
            </button>

            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script>
            var options = {
                "key": "{{ config('services.razorpay.key') }}",
                "amount": "{{ $gateway_data['amount'] }}",
                "currency": "{{ $gateway_data['currency'] }}",
                "name": "{{ config('app.name') }}",
                "description": "Subscription to {{ $plan->name }}",
                "order_id": "{{ $gateway_data['order_id'] }}",
                "handler": function (response){
                    // Submit the response to the callback route
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('teacher.payment.callback', 'razorpay') }}";
                    
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = "{{ csrf_token() }}";
                    form.appendChild(csrfToken);

                    for (var key in response) {
                        if (response.hasOwnProperty(key)) {
                            var hiddenField = document.createElement('input');
                            hiddenField.type = 'hidden';
                            hiddenField.name = key;
                            hiddenField.value = response[key];
                            form.appendChild(hiddenField);
                        }
                    }

                    document.body.appendChild(form);
                    form.submit();
                },
                "prefill": {
                    "name": "{{ auth()->user()->name }}",
                    "email": "{{ auth()->user()->email }}"
                },
                "theme": {
                    "color": "#6366f1"
                }
            };
            var rzp1 = new Razorpay(options);
            
            rzp1.on('payment.failed', function (response){
                alert("Payment failed! " + response.error.description);
            });

            document.getElementById('rzp-button1').onclick = function(e){
                rzp1.open();
                e.preventDefault();
            }

            // Automatically open on load
            window.onload = function() {
                rzp1.open();
            };
            </script>
        @else
            <div class="alert alert-warning">
                Unsupported gateway for client-side processing: {{ $payment->gateway }}
            </div>
        @endif
        
        <div class="mt-4">
            <a href="{{ route('teacher.checkout', $plan->slug) }}" class="text-secondary" style="font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> Go back to checkout
            </a>
        </div>
    </div>
</div>
@endsection
