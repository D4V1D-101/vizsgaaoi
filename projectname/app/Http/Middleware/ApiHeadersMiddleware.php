<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->headers->set('X-API-Version', 'v1.0.0');
        $response->headers->set('X-API-Author', 'Your Name');
        $response->headers->set('X-API-Documentation', 'https://your-api-docs.com');
        $response->headers->set('X-Rate-Limit', '1000');
        $response->headers->set('X-Rate-Limit-Remaining', '999');
        $response->headers->set('Cache-Control', 'no-cache, private');

        if ($request->isMethod('POST')) {
            $response->headers->set('X-Resource-Created', 'true');
        }

        if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $response->headers->set('X-Resource-Updated', 'true');
        }

        if ($request->isMethod('DELETE')) {
            $response->headers->set('X-Resource-Deleted', 'true');
        }

        return $response;
    }
}
