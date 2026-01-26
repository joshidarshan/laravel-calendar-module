@extends('layouts.app')

@section('content')
<h2>Add Field to: {{ $form->name }}</h2>

<form action="{{ route('form-fields.store', $form->id) }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Field Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Field Type</label>
        <select name="type" class="form-control" required>
            <option value="text">Text</option>
            <option value="number">Number</option>
            <option value="email">Email</option>
            <option value="date">Date</option>
            <option value="textarea">Textarea</option>
            <option value="select">Select</option>
        </select>
    </div>

    <button class="btn btn-primary">Save Field</button>
</form>
@endsection
