<?php

namespace CentralityLabs\SparkRoles\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use CentralityLabs\SparkRoles\Middleware\AbstractMiddleware;

class TeamHasRole extends AbstractMiddleware
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
     * @param string                   $role
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (!$this->auth) {
            return $this->forbidden($request);
        }

        $team = $this->auth->user()->currentTeam;

        if (!$team || !$team->isRole($role)) {
            return $this->forbidden($request);
        }

        return $next($request);
    }
}
