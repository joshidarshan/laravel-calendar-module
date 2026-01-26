<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class FormEntryController extends Controller
{
    // Show entries page
    public function index(Form $form)
    {
        return view('entries.index', compact('form'));
    }

    // DATATABLES AJAX (FINAL FIXED)
    public function ajax(Form $form)
    {
        $entries = FormEntry::with('values.field')
            ->where('form_id', $form->id)
            ->latest();

        return DataTables::of($entries)
            ->addIndexColumn()

            // DISPLAY COLUMNS
            ->addColumn('col1', function ($entry) {
                return optional($entry->values->get(0))->value;
            })
            ->addColumn('col2', function ($entry) {
                return optional($entry->values->get(1))->value;
            })
            ->addColumn('col3', function ($entry) {
                return optional($entry->values->get(2))->value;
            })

            // 🔍 SERVER SIDE SEARCH FIX
            ->filterColumn('col1', function ($query, $keyword) {
                $query->whereHas('values', function ($q) use ($keyword) {
                    $q->where('value', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('col2', function ($query, $keyword) {
                $query->whereHas('values', function ($q) use ($keyword) {
                    $q->where('value', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('col3', function ($query, $keyword) {
                $query->whereHas('values', function ($q) use ($keyword) {
                    $q->where('value', 'like', "%{$keyword}%");
                });
            })

            // ACTION BUTTONS
            ->addColumn('action', function ($entry) {
                return '
                    <button class="btn btn-sm btn-info viewEntry" data-id="'.$entry->id.'">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary editEntry" data-id="'.$entry->id.'">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteEntry" data-id="'.$entry->id.'">
                        <i class="bi bi-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // VIEW ENTRY
    public function show(FormEntry $entry)
    {
        $entry->load('values.field');

        return response()->json(
            $entry->values->map(fn($v) => [
                'label' => $v->field->label,
                'value' => $this->formatValue($v->value)
            ])
        );
    }

    // EDIT ENTRY
  // EDIT ENTRY
public function edit(FormEntry $entry)
{
    // Load entry values and their fields
    $entry->load('values.field');

    // Get all fields for the form
    $fields = $entry->form->fields()->orderBy('id')->get();

    $values = [];

    foreach ($fields as $field) {
        // Check if the entry already has a value for this field
        $entryValue = $entry->values->firstWhere('form_field_id', $field->id);

        $values[] = [
            'form_field_id' => $field->id,
            'field' => $field,
            'value' => $entryValue ? $entryValue->value : null, // null if not set
        ];
    }

    return response()->json(['values' => $values]);
}


    // UPDATE ENTRY
    public function update(Request $request, FormEntry $entry)
    {
        foreach ($entry->values as $v) {
            if ($request->has($v->form_field_id)) {
                $v->update([
                    'value' => $request->input($v->form_field_id)
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    // DELETE ENTRY
    public function destroy(FormEntry $entry)
    {
        $entry->delete();
        return response()->json(['success' => true]);
    }

    // FORMAT DATE
    private function formatValue($value)
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return Carbon::parse($value)->format('d-m-Y');
        }
        return $value;
    }
}
