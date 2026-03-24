<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsActive
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && !$request->user()->IsActive) {
            return response()->json(['message' => 'A fiók inaktív.'], 403);
        }

        return $next($request);
    }
}
