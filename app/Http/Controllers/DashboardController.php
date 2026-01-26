<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\FormField;

class DashboardController extends Controller
{
    public function index()
    {
        $totalForms = Form::count();
        $totalEntries = FormEntry::count();
        $totalFields = FormField::count();

        $latestEntries = FormEntry::with('values.field', 'form')
                            ->latest()
                            ->take(5)
                            ->get();

        return view('dashboard', compact(
            'totalForms',
            'totalEntries',
            'totalFields',
            'latestEntries'
        ));
    }
}
