<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FormController extends Controller
{
    public function index()
    {
        return view('forms.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:255|unique:forms,name',
        ]);

        $form = Form::create(['name' => $request->name]);

        return response()->json(['message' => 'Form created successfully!', 'form' => $form]);
    }

    public function edit(Form $form)
    {
        return view('forms.edit', compact('form'));
    }

    public function update(Request $request, Form $form)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:forms,name,' . $form->id,
        ]);

        $form->update(['name' => $request->name]);

        return redirect()->route('forms.index')->with('success', 'Form updated successfully!');
    }

    public function destroy(Form $form)
    {
        $form->delete();

        return response()->json(['message' => 'Form deleted successfully!']);
    }

    public function ajaxList()
    {
        $forms = Form::latest();

        return DataTables::of($forms)
            ->addColumn('actions', function ($form) {
                return '
                    <a href="'.route('form-fields.index', $form->id).'" class="btn btn-sm btn-warning me-1">
                        <i class="bi bi-ui-checks-grid"></i> Fields
                    </a>
                    <a href="'.route('forms.edit', $form->id).'" class="btn btn-sm btn-info me-1">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button onclick="deleteForm('.$form->id.')" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
   public function ajaxSearch(Request $request)
{
    if(!$request->search){
        return response()->json([
            'data' => Form::latest()->take(5)->get()
        ]);
    }

    return response()->json([
        'data' => Form::where('name','like','%'.$request->search.'%')->get()
    ]);
}


}
