<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForumAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $group = $request->route('forumGroup');
        $user = auth()->user();

        if ($group) {
            if ($group->type === 'teacher' && !$user->hasRole('teacher')) {
                abort(403, 'This community is for teachers only.');
            }
            if ($group->type === 'student' && !$user->hasRole('student')) {
                abort(403, 'This community is for students only.');
            }
        }

        return $next($request);
    }
}
