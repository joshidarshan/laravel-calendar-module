@extends('layouts.app')

@section('content')
<h2>Add Field to Form: {{ $form->name }}</h2>

<form action="{{ route('form-fields.store', $form->id) }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Label</label>
        <input type="text" name="label" class="form-control" required>
        @error('label') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Type</label>
        <select name="type" class="form-control" required>
            <option value="text">Text</option>
            <option value="number">Number</option>
            <option value="textarea">Textarea</option>
            <option value="date">Date</option>
            <option value="time">Time</option>
        </select>
        @error('type') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_required" class="form-check-input" id="is_required">
        <label for="is_required" class="form-check-label">Required</label>
    </div>

    <button class="btn btn-success">Add Field</button>
    <a href="{{ route('form-fields.index', $form->id) }}" class="btn btn-secondary">Back</a>
</form>
@endsection
    