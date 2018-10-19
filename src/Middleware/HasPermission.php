<?php

namespace CentralityLabs\SparkRoles\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use CentralityLabs\SparkRoles\Models\Role;
use CentralityLabs\SparkRoles\Middleware\AbstractMiddleware;

class HasPermission extends AbstractMiddleware
{
    /**
     * @var Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Create a new HasPermission instance.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Run the request filter.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $closure
     * @param array|string             $permissions
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        if (!$this->auth) {
            return $this->forbidden($request);
        }

        if (!$this->auth->check()) {
            $guest = Role::whereSlug('guest')->first();

            if (!$guest) {
                return $this->forbidden($request);
            }

            if (!$guest->can($permissions)) {
                return $this->forbidden($request);
            }

            return $next($request);
        }

        $team = $this->auth->user()->currentTeam;
        if (!$this->auth->user()->can($permissions) && !($team && $team->can($permissions))) {
            return $this->forbidden($request);
        }

        return $next($request);
    }
}
