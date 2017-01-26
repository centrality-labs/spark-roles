<?php

namespace ZiNETHQ\SparkRoles\Middleware;

use Closure;
use Laravel\Spark\Spark;
use ZiNETHQ\SparkRoles\Models\Role;
use Exception;
use Log;

class AddDevelopers extends AbstractMiddleware
{
    /**
     * Run the request filter.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $closure
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (config('sparkroles.developer.enable')) {
                $role = Role::where('slug', config('sparkroles.developer.slug'))->first();
                if ($role) {
                    $developers = [];

                    foreach ($role->users as $user) {
                        $developers[] = $user->email;
                    }

                    foreach ($role->teams as $team) {
                        foreach ($team->users as $user) {
                            $developers[] = $user->email;
                        }
                    }
                    Spark::developers(array_merge(Spark::$developers, $developers));
                }
            }
        } catch (Exception $e) {
            Log::error("Error: {$e->getMessage()}\n{$e->getTraceAsString()}");
        }
        return $next($request);
    }
}
