<?php

namespace Modules\Quickbooks\Http\Middleware;

use Closure;

class QuickbooksEnabled
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!setting('quickbooks.enabled', false)) {
            return redirect(route('quickbooks.auth.start'));
        }

        return $next($request);
    }
}
