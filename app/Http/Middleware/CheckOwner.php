<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckOwner
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
        $item = $request->route('item');
        if ($item && isset($item->user_id) && $item->user_id !== Auth::id()) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
