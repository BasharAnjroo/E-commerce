<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $userRole = $request->user()->roles()->first();
        if ($userRole) {
            // Set scope as admin/moderator based on user role
            $request->request->add([
                'scope' => $userRole->title
            ]);
        }

        return $next($request);
    }
    }

