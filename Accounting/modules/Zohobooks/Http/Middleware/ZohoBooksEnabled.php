<?php

namespace Modules\Zohobooks\Http\Middleware;

use Closure;

class ZohoBooksEnabled
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
        if (!setting('zohobooks.enabled', false)) {
            return redirect(route('zohobooks.auth.start'));
        }

        return $next($request);
    }
}
