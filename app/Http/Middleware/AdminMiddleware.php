<?php

namespace App\Http\Middleware;

use App\Enums\ActiveInactive;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('admin');

        if ($user === null || ! $user->relationLoaded('admin')) {
            $user?->load('admin');
        }
        // if(!Auth::guard('admin')->check()) {
        //     dd('not logged in');
        //     return redirect()->route('admin.login');
        // }

        $admin = $user?->admin;

        if ($admin === null || $admin->status->value !== ActiveInactive::ACTIVE->value) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
