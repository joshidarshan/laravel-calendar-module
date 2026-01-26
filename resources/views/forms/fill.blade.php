@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-semibold mb-1">{{ $form->name }}</h3>
                <small class="text-muted">Please fill the form details below</small>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('forms.entries', $form->id) }}" class="btn btn-outline-secondary rounded-pill px-3"> <i
                        class="bi bi-eye"></i> View Entries </a>
                {{-- <a href="{{ route('forms.edit', $form->id) }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-pencil"></i> Edit Name
                </a>

                <a href="{{ route('form-fields.index', $form->id) }}" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-ui-checks-grid"></i> Edit Fields
                </a> --}}

            </div>
        </div>

        <!-- Card -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                @if (session('success'))
                    <div class="alert alert-success rounded-3">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('forms.submit', $form->id) }}" method="POST">
                    @csrf

                    @forelse($fields as $field)
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                {{ $field->label }}
                                @if ($field->is_required)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @if (in_array($field->type, ['text', 'number']))
                                <input type="{{ $field->type }}" name="{{ $field->id }}"
                                    class="form-control form-control-lg rounded-3
                                   @error($field->id) is-invalid @enderror"
                                    value="{{ old($field->id) }}" @if ($field->is_required) required @endif>
                            @elseif($field->type == 'textarea')
                                <textarea name="{{ $field->id }}" rows="3"
                                    class="form-control form-control-lg rounded-3
                                      @error($field->id) is-invalid @enderror"
                                    @if ($field->is_required) required @endif>{{ old($field->id) }}</textarea>
                            @elseif($field->type == 'date')
                                <input type="date" name="{{ $field->id }}"
                                    class="form-control form-control-lg rounded-3
                                   @error($field->id) is-invalid @enderror"
                                    @if ($field->is_required) required @endif>
                            @elseif($field->type == 'time')
                                <input type="time" name="{{ $field->id }}"
                                    class="form-control form-control-lg rounded-3
                                   @error($field->id) is-invalid @enderror"
                                    @if ($field->is_required) required @endif>
                            @endif

                            @error($field->id)
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @empty
                        <div class="alert alert-warning rounded-3 text-center">
                            <i class="bi bi-exclamation-circle"></i>
                            No fields added to this form yet.
                        </div>
                    @endforelse

                    @if ($fields->count() > 0)
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5">
                                <i class="bi bi-send"></i> Submit Form
                            </button>
                        </div>
                    @endif
                </form>

            </div>
        </div>
    </div>
@endsection
