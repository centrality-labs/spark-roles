<?php

namespace CentralityLabs\SparkRoles\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AbstractMiddleware
{
    protected function forbidden($request, $message = 'Forbidden')
    {
        if ($request->ajax()) {
            return response($message, 403);
        }
        return abort(403, $message);
    }

    protected function badrequest($request, $message = 'Bad request')
    {
        if ($request->ajax()) {
            return response($message, 400);
        }
        return abort(400, $message);
    }
}
