<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormEntry extends Model
{
    protected $fillable = ['form_id'];

    // Relationship with FormEntryValue
    public function values()
    {
        return $this->hasMany(FormEntryValue::class, 'form_entry_id');
    }

    // Relationship with Form
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
