@extends('layouts.app')

@section('content')
<div class="container py-5 animate-in">
    <div class="max-width-800 mx-auto">
        <div class="page-header mb-5">
            <h1 class="page-title">Create Course Bundle</h1>
            <p class="page-subtitle">Select courses to bundle and invite collaborators for revenue sharing.</p>
        </div>

        <form action="{{ route('teacher.bundles.store') }}" method="POST" class="card p-5">
            @csrf

            <div class="form-group mb-4">
                <label class="form-label">Bundle Title</label>
                <input type="text" name="title" class="form-input" placeholder="e.g. Complete GATE CS Master Pack" required>
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input" rows="3" placeholder="Explain the benefits of this bundle..."></textarea>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Bundle Price (₹)</label>
                        <input type="number" name="bundle_price" class="form-input" placeholder="1500" required>
                    </div>
                </div>
            </div>

            <div class="form-group mb-5">
                <label class="form-label">Select Your Courses to Include</label>
                <div class="grid-2 mt-2">
                    @foreach($courses as $course)
                    <label class="selection-card p-3 d-flex align-items-center">
                        <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" class="me-3">
                        <div>
                            <div class="font-weight-bold">{{ $course->title }}</div>
                            <small class="text-muted">Price: ₹{{ $course->price }}</small>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group mb-5">
                <label class="form-label">Invite Collaborators (Optional)</label>
                <p class="text-muted small mb-3">Invite other teachers to include their courses in this bundle. Define their revenue share.</p>
                
                <div id="collaborators-list">
                    <!-- Dynamic collaborators will be added here -->
                </div>
                
                <button type="button" class="btn btn-outline btn-sm mt-2" onclick="addCollaborator()">
                    <i class="fas fa-user-plus me-2"></i> Add Collaborator
                </button>
            </div>

            <div class="d-flex justify-content-between border-top pt-4">
                <a href="{{ route('teacher.bundles') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-5">Create & Send Invites</button>
            </div>
        </form>
    </div>
</div>

<template id="collaborator-template">
    <div class="collaborator-row card p-3 bg-light mb-3 border-0">
        <div class="row align-items-end">
            <div class="col-md-6">
                <label class="form-label small">Select Teacher</label>
                <select name="collaborators[IDX][teacher_id]" class="form-input">
                    @foreach($otherTeachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Revenue Share (%)</label>
                <input type="number" name="collaborators[IDX][share]" class="form-input" placeholder="20" min="1" max="100">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-block" onclick="this.closest('.collaborator-row').remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    let collabIndex = 0;
    function addCollaborator() {
        const template = document.getElementById('collaborator-template').innerHTML;
        const html = template.replace(/IDX/g, collabIndex++);
        document.getElementById('collaborators-list').insertAdjacentHTML('beforeend', html);
    }
</script>

<style>
    .selection-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .selection-card:hover { border-color: #3b82f6; background: #f8fafc; }
    .selection-card input:checked + div { color: #3b82f6; }
</style>
@endsection
