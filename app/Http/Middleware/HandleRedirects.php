<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();
        
        $redirect = Redirect::where('old_path', $path)->first();
        
        if ($redirect) {
            return redirect($redirect->new_path, $redirect->status_code);
        }
        
        return $next($request);
    }
}
