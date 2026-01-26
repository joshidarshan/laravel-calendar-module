@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center mt-5">
    <div class="card shadow-sm rounded-4 border-0 add-field-card">
        <!-- Card Header -->
        <div class="card-header bg-gradient-to-right text-white d-flex justify-content-between align-items-center rounded-top py-2 px-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-plus-circle me-2"></i> Add Field to "{{ $form->name }}"
            </h5>
            <a href="{{ route('form-fields.index', $form->id) }}" class="btn btn-danger btn-sm rounded-circle">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>

        <!-- Card Body -->
        <div class="card-body p-4">
            <form method="POST" action="{{ route('form-fields.store', $form->id) }}" class="needs-validation" novalidate>
                @csrf

                <!-- Field Label -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-info">
                        <i class="bi bi-card-text me-2"></i> Field Label
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="label" class="form-control form-control-lg rounded-pill border-info" placeholder="Enter field label" required>
                    @error('label')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Field Type -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-info">
                        <i class="bi bi-ui-checks-grid me-2"></i> Field Type
                        <span class="text-danger">*</span>
                    </label>
                    <select name="type" class="form-select form-select-lg rounded-pill border-info" required>
                        <option value="">Select type</option>
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="textarea">Textarea</option>
                        <option value="date">Date</option>
                        <option value="time">Time</option>
                    </select>
                    @error('type')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Required -->
                <div class="form-check mb-4">
                    <input type="checkbox" name="is_required" class="form-check-input" value="1">
                    <label class="form-check-label fw-semibold">Required Field</label>
                </div>

                <!-- Save Button -->
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-success btn-lg rounded-pill w-60">
                        <i class="bi bi-check-circle me-2"></i> Save Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Card Style */
    .add-field-card {
        width: 80%;
        max-width: 800px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    /* Header Gradient */
    .bg-gradient-to-right {
        background: linear-gradient(90deg, #6366f1, #4f46e5);
    }

    /* Form Inputs */
    .form-control, .form-select {
        border-width: 2px;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 6px rgba(99,102,241,0.25);
    }

    /* Buttons */
    .btn-success {
        background-color: #10b981;
        border: none;
        transition: all 0.2s;
    }
    .btn-success:hover {
        background-color: #059669;
    }

    .btn-danger {
        background-color: #ef4444;
        color: #fff;
        border: none;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-danger:hover {
        background-color: #dc2626;
        color: #fff;
    }

    /* Button Width */
    .w-60 { width: 60%; }

    /* Responsive */
    @media(max-width: 900px){
        .add-field-card { width: 95%; }
        .w-60 { width: 100%; }
    }
</style>
@endpush
