<?php

namespace Sevenspan\CodeGenerator\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeCodeGenerator
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the application is in the production environment
     * and whether authorization is required for the code generator. If authorization
     * is required but not enabled, the request is aborted with a 403 status code.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request.
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next  The next middleware in the pipeline.
     * @return \Symfony\Component\HttpFoundation\Response  The HTTP response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the application is running in the production environment
        if (app()->environment('production')) {
            // Abort the request with a 403 status code if authorization is required but not enabled
            if (!config('code_generator.require_auth_in_production')) {
                abort(403);
            }
        }

        // Allow the request to proceed to the next middleware or controller
        return $next($request);
    }
}
