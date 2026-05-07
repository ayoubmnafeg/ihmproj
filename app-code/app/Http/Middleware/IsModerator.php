<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsModerator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin() && ! $request->user()?->isModerator()) {
            abort(403);
        }

        return $next($request);
    }
}
