@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-dark">Fields for Form: {{ $form->name }}</h2>
    <a href="{{ route('form-fields.create', $form->id) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Field
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm rounded-4 border-0 bg-light">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Label</th>
                    <th>Type</th>
                    <th>Required</th>
                    <th width="25%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fields as $field)
                <tr>
                    <td>{{ $field->id }}</td>
                    <td>{{ $field->label }}</td>
                    <td>{{ ucfirst($field->type) }}</td>
                    <td>{{ $field->is_required ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('form-fields.edit', $field->id) }}" class="btn btn-sm btn-info me-1">Edit</a>
                        <form action="{{ route('form-fields.destroy', $field->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this field?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No fields found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
