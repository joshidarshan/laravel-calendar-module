<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FormFieldController extends Controller
{
    public function index(Form $form)
    {
        return view('fields.index', compact('form'));
    }

    public function create(Form $form)
    {
        return view('fields.create', compact('form'));
    }

    public function store(Request $request, Form $form)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,textarea,date,time',
            'is_required' => 'nullable|boolean',
        ]);

        $form->fields()->create([
            'label' => $request->label,
            'type' => $request->type,
            'is_required' => $request->has('is_required'),
        ]);

        return redirect()->route('form-fields.index', $form->id)
                         ->with('success', 'Field added successfully!');
    }

    public function edit(Form $form, FormField $field)
    {
        return view('fields.edit', compact('form', 'field'));
    }

    public function update(Request $request, Form $form, FormField $field)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,textarea,date,time',
        ]);

        $field->update([
            'label' => $request->label,
            'type' => $request->type,
            'is_required' => $request->has('is_required'),
        ]);

        return redirect()->route('form-fields.index', $form->id)
                         ->with('success', 'Field updated successfully!');
    }

    public function destroy(Form $form, FormField $field)
    {
        $field->delete();
        return redirect()->route('form-fields.index', $form->id)
                         ->with('success', 'Field deleted successfully!');
    }

    public function ajaxList(Form $form)
    {
        $fields = $form->fields()->select(['id', 'label', 'type', 'is_required']);

        return DataTables::of($fields)
            ->addColumn('is_required', fn($field) => $field->is_required ? 'Yes' : 'No')
            ->addColumn('actions', function ($field) use ($form) {
                return '
                    <a href="'.route('form-fields.edit', [$form->id, $field->id]).'" 
                       class="btn btn-sm btn-warning me-1">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="'.route('form-fields.destroy', [$form->id, $field->id]).'" 
                          method="POST" style="display:inline-block">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this field?\')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
