<table class="table align-middle table-hover">
    <thead class="table-secondary">
        <tr>
            <th>ID</th>
            <th>Form Name</th>
            <th width="35%">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($forms as $form)
            <tr>
                <td>{{ $form->id }}</td>
                <td>{{ $form->name }}</td>
                <td>
                    <a href="{{ route('form-fields.create', $form->id) }}" class="btn btn-sm btn-outline-warning">Add Fields</a>
                    <a href="{{ route('forms.edit', $form->id) }}" class="btn btn-sm btn-outline-info">Edit</a>
                    <button class="btn btn-sm btn-outline-danger ajax-delete" data-url="{{ route('forms.destroy', $form->id) }}">Delete</button>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="text-center text-muted">No forms found</td></tr>
        @endforelse
    </tbody>
</table>
