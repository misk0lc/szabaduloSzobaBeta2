<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->IsAdmin) {
            return response()->json(['message' => 'Hozzáférés megtagadva. Admin jogosultság szükséges.'], 403);
        }

        return $next($request);
    }
}
