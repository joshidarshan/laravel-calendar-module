@extends('layouts.app')
@section('topbar-title', 'Forms')
@section('topbar-buttons')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <button class="btn btn-primary btn-lg rounded-pill" data-bs-toggle="modal" data-bs-target="#createFormModal">
        <i class="bi bi-plus-circle me-1"></i> New Form
    </button>
@endsection

@section('content')
    {{-- <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-dark">Forms</h2>
    <button class="btn btn-primary btn-lg rounded-pill" data-bs-toggle="modal" data-bs-target="#createFormModal">
        <i class="bi bi-plus-circle me-1"></i> New Form
    </button>
</div> --}}

    <div class="card shadow-sm rounded-4 border-0 bg-white">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-nowrap" id="formsTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th width="30%">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Form Modal -->
    <!-- Create Form Modal -->
    <div class="modal fade" id="createFormModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <form id="createForm" class="needs-validation" novalidate>
                @csrf
                <div class="modal-content rounded-4 shadow-lg border-0">

                    <!-- Header -->
                    <div
                        class="modal-header bg-primary text-white py-3 px-4 rounded-top d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="bi bi-file-earmark-plus-fill me-2"></i>
                            <span>Create New Form</span>
                        </h5>
                        <!-- Close button on right -->
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-primary">
                                <i class="bi bi-card-text me-2"></i> Form Name
                                <span class="text-danger">*</span> <!-- Required field -->
                            </label>
                            <input type="text" name="name"
                                class="form-control form-control-lg rounded-pill border-primary"
                                placeholder="Enter form name" required>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer px-4 pb-4 border-0">
                        <button type="submit" class="btn btn-success btn-lg rounded-pill w-100">
                            <i class="bi bi-check-circle me-2"></i> Save Form
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>


    </div>
    </form>
    </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {
            // Modern DataTable with page length options
            let table = $('#formsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('forms.ajax.list') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                pageLength: 5,
                lengthMenu: [
                    [5, 10, 20, 50, 100],
                    [5, 10, 20, 50, 100]
                ],
                lengthChange: true,
                responsive: true,
                language: {
                    paginate: {
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>'
                    },
                    processing: '<div class="spinner-border spinner-border-sm text-primary"></div> Loading...'
                }
            });

            // Create Form AJAX
            $('#createForm').submit(function(e) {
                e.preventDefault();
                $('#formNameError').text('');
                $.post("{{ route('forms.store') }}", $(this).serialize(), function(res) {
                    $('#createFormModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: true
                    });
                    table.ajax.reload(null, false);
                    $('#createForm')[0].reset();
                }).fail(function(err) {
                    if (err.status === 422) {
                        $('#formNameError').text(err.responseJSON.errors.name[0]);
                    } else {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                });
            });
        });

        // Delete Form
        function deleteForm(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the form!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((res) => {
                if (res.isConfirmed) {
                    $.ajax({
                        url: `/forms/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#formsTable').DataTable().ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire('Error', 'Unable to delete form', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
