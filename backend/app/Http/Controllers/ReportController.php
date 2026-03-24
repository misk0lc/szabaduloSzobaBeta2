<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Title'   => 'required|string|max:100',
            'Message' => 'required|string|max:2000',
            'Page'    => 'nullable|string|max:100',
        ]);

        $report = Report::create([
            'UserID'  => $request->user()->UserID,
            'Title'   => $validated['Title'],
            'Message' => $validated['Message'],
            'Page'    => $validated['Page'] ?? null,
        ]);

        return response()->json(['message' => 'Köszönjük a visszajelzést!', 'report' => $report], 201);
    }
}
