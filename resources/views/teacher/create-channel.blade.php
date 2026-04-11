@extends('layouts.app')

@push('styles')
<style>
    .teacher-create-channel {
        max-width: 600px;
        margin: 40px auto;
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
            <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">Create Channel</button>
        </form>
    </div>
</div>
@endsection
