<?php

namespace CentralityLabs\SparkRoles\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use CentralityLabs\SparkRoles\Middleware\AbstractMiddleware;

class IsCurrentTeam extends AbstractMiddleware
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
     * @param  int $argument_name
     * @return mixed
     */
    public function handle($request, Closure $next, $argument_name)
    {
        if (!$this->auth) {
            return $this->forbidden($request);
        }

        $currentTeam = $this->auth->user()->currentTeam;

        if (!$currentTeam) {
            return $this->forbidden($request);
        }

        if (!array_key_exists($argument_name, $request->route()->parameters())) {
            return $this->badrequest($request, "Argument name {$argument_name} not found in request parameters.");
        }

        if ($currentTeam !== $request->route()->getParameter($argument_name)) {
            return $this->forbidden($request);
        }

        return $next($request);
    }
}
