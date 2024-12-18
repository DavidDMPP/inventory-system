<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToPanelMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Jika mencoba akses /admin tapi bukan admin
        if ($request->is("admin*") && !$user?->isAdmin()) {
            return redirect("/user/dashboard");
        }

        // Jika sudah login, arahkan ke panel yang sesuai
        if ($user && $request->is("/")) {
            return $user->isAdmin() ? 
                redirect("/admin/dashboard") : 
                redirect("/user/dashboard");
        }

        return $next($request);
    }
}
