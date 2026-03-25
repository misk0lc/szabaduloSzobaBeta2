<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Bejelentkezett felhasználótól (auth mögött)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Title'    => 'required|string|max:100',
            'Category' => 'sometimes|string|max:50',
            'Message'  => 'required|string|max:2000',
            'Page'     => 'nullable|string|max:100',
        ]);

        $report = Report::create([
            'UserID'   => $request->user()->UserID,
            'Title'    => $validated['Title'],
            'Category' => $validated['Category'] ?? 'bug',
            'Message'  => $validated['Message'],
            'Page'     => $validated['Page'] ?? null,
        ]);

        return response()->json(['message' => 'Köszönjük a visszajelzést!', 'report' => $report], 201);
    }

    // Bejelentkezés nélkül (login oldalról)
    public function storePublic(Request $request)
    {
        $validated = $request->validate([
            'Title'        => 'required|string|max:100',
            'Category'     => 'required|string|max:50',
            'ContactEmail' => 'nullable|email|max:100',
            'Message'      => 'required|string|max:2000',
            'Page'         => 'nullable|string|max:100',
        ]);

        $report = Report::create([
            'UserID'       => null,
            'Title'        => $validated['Title'],
            'Category'     => $validated['Category'],
            'ContactEmail' => $validated['ContactEmail'] ?? null,
            'Message'      => $validated['Message'],
            'Page'         => $validated['Page'] ?? 'login',
        ]);

        return response()->json(['message' => 'Köszönjük a visszajelzést!', 'report' => $report], 201);
    }
}
