<?php

namespace App\Http\Middleware;

use Closure;

class CheckConstellation
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

        if ($request->id > 12) {
            return redirect()->to('home')->withErrors(['constellation' => '星座只有12個']);
        }

        return $next($request);
    }
}
