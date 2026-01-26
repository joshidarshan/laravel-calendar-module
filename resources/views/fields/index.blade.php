        @extends('layouts.app')

        @section('content')
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>{{ $form->name }} - Fields</h4>
                <a href="{{ route('form-fields.create', $form->id) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Field
                </a>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="fieldsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Label</th>
                                    <th>Type</th>
                                    <th>Required</th>
                                    <th width="25%">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endsection

        @push('scripts')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

        <script>
        $(function(){
            $('#fieldsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('form-fields.ajax', $form->id) }}",
                columns: [
    { data: 'id', name: 'id' },
    { data: 'label', name: 'label' },
    { data: 'type', name: 'type', render: function(d){
        let colors = { text:'primary', number:'success', textarea:'warning', date:'info', time:'secondary'};
        return `<span class="badge bg-${colors[d]||'dark'}">${d.toUpperCase()}</span>`;
    }},
    { data: 'is_required', name: 'is_required' },
    { 
        data: 'actions', 
        name: 'actions', 
        orderable: false, 
        searchable: false, 
        className: 'text-end' // Align actions to right
    }
]

            });
        });
        </script>
        @endpush
