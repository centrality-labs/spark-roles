<?php

namespace CentralityLabs\SparkRoles\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use CentralityLabs\SparkRoles\Middleware\AbstractMiddleware;

class HasCurrentTeam extends AbstractMiddleware
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
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->auth) {
            return $this->forbidden($request);
        }

        if (!$this->auth->user()->currentTeam) {
            return $this->forbidden($request);
        }

        return $next($request);
    }
}
