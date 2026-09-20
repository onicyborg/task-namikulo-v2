<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // BinaryFileResponse (used by Excel::download) does not expose
        // Laravel's fluent header() helper. Set headers through Symfony's
        // response header bag so normal and file responses are both handled.
        $response->headers->set('Cache-Control', 'nocache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
