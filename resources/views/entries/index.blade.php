@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h5>{{ $form->name }} – Entries</h5>
        <a href="{{ route('forms.fill', $form->id) }}" class="btn btn-primary btn-lg rounded-pill">
            <i class="bi bi-plus-circle"></i> Add Entry
        </a>
    </div>

    <table class="table table-bordered" id="entriesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Field 1</th>
                <th>Field 2</th>
                <th>Field 3</th>
                <th width="120">Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- VIEW / EDIT MODAL -->
<div class="modal fade" id="entryModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Entry Details</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="entryBody"></div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="saveEntry">Save Changes</button>
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

    let table = $('#entriesTable').DataTable({
        processing:true,
        serverSide:true,
        ajax:"{{ route('forms.entries.ajax', $form->id) }}",
        columns:[
            {data:'DT_RowIndex', searchable:false, orderable:false},
            {data:'col1', name:'col1'},
            {data:'col2', name:'col2'},
            {data:'col3', name:'col3'},
            {data:'action', searchable:false, orderable:false}
        ],
        pageLength:10,
        lengthMenu:[10,25,50,100]
    });

    // VIEW ENTRY
    $(document).on('click','.viewEntry', function(){
        let id = $(this).data('id');
        $.get('/entries/'+id, function(res){
            let html = '';
            res.forEach(r=>{
                html += `<p><b>${r.label}</b>: ${r.value}</p>`;
            });
            $('#entryBody').html(html);
            $('#entryModal').modal('show');
            $('#saveEntry').hide();
        });
    });

    // EDIT ENTRY
  // EDIT ENTRY
// EDIT ENTRY
// EDIT ENTRY
$(document).on('click','.editEntry', function(){
    let id = $(this).data('id');
    $.get('/entries/'+id+'/edit', function(res){
        let html = '';
        res.values.forEach(v=>{
            let inputType = 'text'; // default
            let tag = 'input';
            let value = v.value ?? '';

            switch(v.field.type){
                case 'number':
                    inputType = 'number';
                    break;
                case 'date':
                    inputType = 'date';
                    break;
                case 'time':
                    inputType = 'time';
                    break;
                case 'textarea':
                case 'address': // treat address as textarea
                    tag = 'textarea';
                    break;
            }

           html += `<div class="mb-2">
    <label><b>${v.field.label}${v.field.is_required ? ' <span class="text-danger">*</span>' : ''}</b></label>
    ${tag === 'input' 
        ? `<input type="${inputType}" class="form-control" name="${v.form_field_id}" 
            value="${value}" 
            placeholder="${value ? '' : v.field.label + ' is empty'}"
            ${v.field.is_required ? 'required' : ''}>`
        : `<textarea class="form-control" name="${v.form_field_id}" 
            placeholder="${value ? '' : v.field.label + ' is empty'}"
            ${v.field.is_required ? 'required' : ''}>${value}</textarea>`
    }
</div>`;

        });

        $('#entryBody').html(html);
        $('#entryModal').modal('show');

        // Save button click
        $('#saveEntry').show().off('click').on('click', function(){

            // ✅ Check required fields before submit
            let valid = true;
            $('#entryBody').find('input, textarea').each(function(){
                if($(this).prop('required') && !$(this).val()){
                    $(this).addClass('is-invalid');
                    valid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if(!valid){
                Swal.fire('Error','Please fill all required fields','error');
                return;
            }

            $.ajax({
                url:'/entries/'+id,
                type:'PUT',
                data:$('#entryBody').find('input, textarea').serialize(),
                success:function(){
                    table.ajax.reload(null,false);
                    $('#entryModal').modal('hide');
                    Swal.fire('Updated!','','success');
                }
            });
        });
    });
});




    // DELETE ENTRY
    $(document).on('click','.deleteEntry', function(){
        let id = $(this).data('id');
        Swal.fire({
            title:'Delete entry?',
            icon:'warning',
            showCancelButton:true
        }).then(result=>{
            if(result.isConfirmed){ 
                $.ajax({
                    url:'/entries/'+id,
                    type:'DELETE',
                    success:function(){
                        table.ajax.reload(null,false);
                        Swal.fire('Deleted!','','success');
                    }
                });
            }
        });
    });

});
</script>
@endpush
