<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsTrainer
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'trainer') {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}
