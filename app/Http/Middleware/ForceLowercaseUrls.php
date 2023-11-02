<?php

namespace App\Http\Middleware;

use Closure;

class ForceLowercaseUrls
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
        $url = $this->isUppercaseUrl($request);
        if ($url) {
            return redirect($url)->setStatusCode(301);
        }
        return $next($request);
    }

    private function isUppercaseUrl($request) {
        // Grab requested URL
        $url = $request->path();
        $queryString = $request->getQueryString();

        // If URL contains a period, halt (likely contains a filename and filenames are case specific)
        if ( preg_match('/[\.]/', $url) ) {
            return false;
        }
        // If URL contains a question mark, halt (likely contains a query variable)
        if ( preg_match('/[\?]/', $url) ) {
            return false;
        }
        if ( preg_match('/[A-Z]/', $url) ) {
            // Convert URL to lowercase
            return url(strtolower($url)) . ($queryString ? '?' . $queryString : '');
        }
        return false;
    }
}
