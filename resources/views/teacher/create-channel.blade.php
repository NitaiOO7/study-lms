@extends('layouts.app')

@push('styles')
<style>
    .teacher-create-channel {
        max-width: 800px;
        margin: 40px auto;
    }
    .plan-card {
        border: 2px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        background: #1e293b;
    }
    .plan-card:hover {
        border-color: #3b82f6;
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }
    .plan-card input[type="radio"] {
        display: none;
    }
    .plan-card input[type="radio"]:checked + label .plan-content {
        border: 2px solid #3b82f6;
        background: rgba(59, 130, 246, 0.05);
    }
    .plan-card input[type="radio"]:checked + label {
        border-color: #3b82f6;
    }
    .plan-header {
        background: #1e293b;
        padding: 20px;
        border-bottom: 1px solid #334155;
    }
    .plan-price {
        font-size: 2rem;
        font-weight: 800;
        color: #3b82f6;
    }
    .plan-features li {
        margin-bottom: 10px;
        list-content: '✓';
        padding-left: 10px;
    }
</style>
@endpush

@section('content')
<div class="container animate-in text-center" style="padding-top: 60px;">
    <div class="teacher-create-channel card">
        <div style="font-size: 3rem; color: var(--primary); margin-bottom: 20px;"><i class="fas fa-tv"></i></div>
        <h1 class="page-title" style="font-size: 1.8rem; margin-bottom: 10px;">Setup Your Teacher Channel</h1>
        <p class="text-secondary" style="margin-bottom: 30px;">You need to create your channel portal before you can upload courses or study materials.</p>

        <form action="{{ route('teacher.channel.store') }}" method="POST" enctype="multipart/form-data" style="text-align: left;">
            @csrf
            <div class="form-group">
                <label class="form-label">Channel Name</label>
                <input type="text" name="name" class="form-input" placeholder="e.g., Prof. Smith's Physics Classes" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-textarea" placeholder="Tell students about your teaching methodology..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Channel Logo (Optional)</label>
                <input type="file" name="logo" class="form-input" accept="image/*">
            </div>

            <div class="form-group mt-5">
                <label class="form-label" style="font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; display: block;">Select Your Channel Plan</label>
                <div class="grid-2" style="gap: 20px;">
                    @foreach($plans as $plan)
                    <div class="plan-card" style="border-radius: 12px; overflow: hidden; position: relative;">
                        <input type="radio" name="plan_id" value="{{ $plan->id }}" id="plan_{{ $plan->id }}" {{ $loop->first ? 'checked' : '' }}>
                        <label for="plan_{{ $plan->id }}" style="cursor: pointer; width: 100%; height: 100%; display: block;">
                            <div class="plan-header text-center">
                                <h3 style="margin: 0; color: #fff; font-size: 1.4rem;">{{ $plan->name }}</h3>
                            </div>
                            <div class="plan-body p-4 text-center">
                                <div class="plan-price">₹{{ number_format($plan->price, 0) }}</div>
                                <div class="text-secondary small mb-4">per {{ $plan->duration_days }} days</div>
                                <ul class="plan-features" style="text-align: left; list-style: none; padding: 0; margin: 0;">
                                    @php $features = is_string($plan->features) ? json_decode($plan->features) : $plan->features; @endphp
                                    @foreach($features as $feature)
                                    <li style="margin-bottom: 8px; font-size: 0.9rem; color: #94a3b8;"><i class="fas fa-check text-primary me-2"></i> {{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">Create Channel</button>
        </form>
    </div>
</div>
@endsection
