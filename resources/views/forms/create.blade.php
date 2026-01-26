@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="fw-semibold mb-4">Create New Form</h3>

    <div class="card shadow-sm border-0 rounded-4 bg-light p-4">
        <form id="createForm">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Form Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Save Form
            </button>
        </form>
    </div>

    <div class="mt-4" id="formList">
        <!-- Existing forms will be loaded here via AJAX -->
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Load forms initially
    loadForms();

    // Create form AJAX
    $('#createForm').submit(function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('forms.store') }}",
            method: "POST",
            data: formData,
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message ?? 'Form created successfully',
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#createForm')[0].reset();
                loadForms(); // reload forms after create
            },
            error: function(xhr) {
                let errMsg = xhr.responseJSON?.errors?.name ?? 'Something went wrong';
                Swal.fire('Error', errMsg, 'error');
            }
        });
    });

    // Load forms list via AJAX
    function loadForms() {
        $.ajax({
            url: "{{ route('forms.index') }}",
            method: "GET",
            success: function(res) {
                $('#formList').html(res); // Make sure your controller returns a partial view
            }
        });
    }

    // Optional: AJAX delete
    $(document).on('click', '.ajax-delete', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        let row = $(this).closest('tr');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: res.message ?? 'Deleted successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        row.fadeOut(300);
                    }
                });
            }
        });
    });

});
</script>
@endpush
