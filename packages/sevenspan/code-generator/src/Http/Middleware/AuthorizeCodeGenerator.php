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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {
            if (!config('code_generator.require_auth_in_production')) {
                abort(403);
            }
        }
        return $next($request);
    }
}
