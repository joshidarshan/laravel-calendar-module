@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center mt-5">
    <div class="card shadow-sm rounded-4 border-0 small-card">
        <!-- Card Header -->
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top py-2 px-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-pencil-square me-2"></i> Edit Form
            </h5>
            <a href="{{ route('forms.index') }}" class="btn btn-danger btn-sm rounded-pill fw-semibold">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </a>
        </div>

        <!-- Card Body -->
        <div class="card-body p-4">
            <form action="{{ route('forms.update', $form->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold text-primary">
                        <i class="bi bi-card-text me-2"></i> Form Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name" 
                           class="form-control form-control-lg rounded-pill border-primary" 
                           value="{{ old('name', $form->name) }}" 
                           placeholder="Enter form name" required>
                    @error('name')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Update Button -->
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-success btn-lg rounded-pill w-60">
                        <i class="bi bi-check-circle me-2"></i> Update Form
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Small Card Style */
    .small-card {
        width: 400px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    /* Card Header */
    .card-header {
        background: linear-gradient(90deg, #4f46e5, #6366f1);
        color: #fff;
        font-size: 1rem;
    }

    /* Form Input */
    .form-control {
        border-width: 2px;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 6px rgba(79,70,229,0.25);
    }

    /* Update Button */
    .btn-success {
        background-color: #22c55e;
        border: none;
        transition: all 0.2s;
    }
    .btn-success:hover {
        background-color: #16a34a;
    }

    /* Cancel Button */
    .btn-danger {
        background-color: #ef4444;
        color: #fff;
        border: none;
        transition: all 0.2s;
    }
    .btn-danger:hover {
        background-color: #dc2626;
        color: #fff;
    }

    /* Button Width */
    .w-60 { width: 60%; }

    /* Responsive */
    @media(max-width: 480px){
        .small-card { width: 90%; }
        .w-60 { width: 100%; }
    }
</style>
@endpush
