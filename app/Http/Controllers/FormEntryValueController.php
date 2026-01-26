<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormEntry;
use App\Models\FormEntryValue;


class FormEntryValueController extends Controller
{
    //
     public function fill(Form $form)
    {
        $fields = $form->fields()->orderBy('id')->get();

        return view('forms.fill', compact('form', 'fields'));
    }
 public function submit(Request $request, Form $form)
{
    // 1️⃣ Validation rules for required fields only
    $rules = [];
    foreach ($form->fields as $field) {
        if ($field->is_required) {
            $rules[$field->id] = 'required';
        }
    }
    $request->validate($rules);

    // 2️⃣ Create form entry
    $formEntry = FormEntry::create([
        'form_id' => $form->id
    ]);

    // 3️⃣ Save ALL field values (even optional empty ones)
    foreach ($form->fields as $field) {
        $value = $request->input($field->id);

        FormEntryValue::create([
            'form_entry_id' => $formEntry->id,
            'form_field_id' => $field->id,
            'value' => $value !== null && $value !== '' ? $value : null, // store null if empty
        ]);
    }

    return redirect()
        ->route('forms.fill', $form->id)
        ->with('success', 'Form submitted successfully!');
}


}
