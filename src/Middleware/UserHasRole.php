<?php

namespace CentralityLabs\SparkRoles\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use CentralityLabs\SparkRoles\Middleware\AbstractMiddleware;

class UserHasRole extends AbstractMiddleware
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
        if (!$this->auth || !$this->auth->user()->isRole($role)) {
            return $this->forbidden($request);
        }

        return $next($request);
    }
}
