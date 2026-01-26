<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormEntryValue extends Model
{
  protected $fillable = ['form_entry_id', 'form_field_id', 'value'];

    public function field()
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }
}