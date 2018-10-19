<?php
namespace CentralityLabs\SparkRoles\Facades;

use Illuminate\Support\Facades\Facade;
use CentralityLabs\SparkRoles\SparkRoles as Roles;

class SparkRoles extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Roles::class;
    }
}
