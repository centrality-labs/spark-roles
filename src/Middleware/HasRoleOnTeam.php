<?php

namespace ZiNETHQ\SparkRoles\Middleware;

use Closure;
use ZiNETHQ\SparkRoles\Middleware\AbstractMiddleware;

class HasRoleOnTeam extends AbstractMiddleware
{
    /**
     *  Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        $authorizedRoles = array_map('trim', explode('|', $roles));
        $user = $request->user();
        $roleOnTeam = $user->roleOn($user->currentTeam);

        if (!in_array($roleOnTeam, $authorizedRoles)) {
            return $this->forbidden($request);
        }

        return $next($request);
    }
}
