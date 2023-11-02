<?php

namespace App\Http\Middleware;

use App\Redirect;
use Closure;
use Illuminate\Support\Str;

class CheckRedirects
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
        $url = $request->getRequestUri();
        $redirect = Redirect::where('from', $url)->first();
        if ($redirect) {
            $to = Str::startsWith($redirect->to, '/') ? url($redirect->to) : $redirect->to;
            return redirect($to, 301);
        }
        return $next($request);
    }
}
