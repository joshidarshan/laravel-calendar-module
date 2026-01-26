@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center mt-5">
    <div class="card shadow-sm rounded-4 border-0 medium-card">
        <!-- Card Header -->
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top py-2 px-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-pencil-square me-2"></i> Edit Field ({{ $field->label }})
            </h5>
            <a href="{{ route('form-fields.index', $field->form_id) }}" class="btn btn-danger btn-sm rounded-circle">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>

        <!-- Card Body -->
        <div class="card-body p-4">
            <form method="POST" action="{{ route('form-fields.update', [$form->id, $field->id]) }}" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <!-- Field Label -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-primary">
                        <i class="bi bi-card-text me-2"></i> Field Label
                        @if($field->is_required)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <input type="text"
                           name="label"
                           class="form-control form-control-lg rounded-pill border-primary"
                           value="{{ old('label', $field->label) }}"
                           placeholder="Enter field label"
                           required>
                    @error('label')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Field Type -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-primary">
                        <i class="bi bi-ui-checks-grid me-2"></i> Field Type
                        <span class="text-danger">*</span>
                    </label>
                    <select name="type" class="form-select form-select-lg rounded-pill border-primary" required>
                        @foreach(['text','number','textarea','date','time'] as $type)
                            <option value="{{ $type }}" {{ $field->type === $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Required -->
                <div class="form-check mb-4">
                    <input type="checkbox"
                           name="is_required"
                           class="form-check-input"
                           value="1"
                           {{ $field->is_required ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold">Required Field</label>
                </div>

                <!-- Update Button -->
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-success btn-lg rounded-pill w-60">
                        <i class="bi bi-check-circle me-2"></i> Update Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Medium Card Style */
    .medium-card {
        width: 550px; /* wider than before */
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
    .form-control, .form-select {
        border-width: 2px;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 6px rgba(79,70,229,0.25);
    }

    /* Buttons */
    .btn-success {
        background-color: #22c55e;
        border: none;
        transition: all 0.2s;
    }
    .btn-success:hover {
        background-color: #16a34a;
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
    @media(max-width: 600px){
        .medium-card { width: 90%; }
        .w-60 { width: 100%; }
    }
</style>
@endpush
