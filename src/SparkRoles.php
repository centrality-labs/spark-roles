<?php
namespace CentralityLabs\SparkRoles;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Collection;
use Laravel\Spark\Spark;

use CentralityLabs\SparkRoles\Models\Role;
use CentralityLabs\SparkRoles\Traits\CanHaveRoles;

class SparkRoles
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

    public static function roleModel()
    {
        return config('sparkroles.models.role');
    }

    public static function permissionModel()
    {
        return config('sparkroles.models.permission');
    }

    /**
     * Checks if model has the given permissions.
     *
     * @param array|string $permissions
     *
     * @return bool
     */
    public function can($permissions)
    {
        if ($this->auth->check()) {
            return $this->getModels()->contains(function ($value, $key) use ($permissions) {
                return $value->can($permissions);
            });
        } else {
            $guest = Role::whereSlug('guest')->first();

            if ($guest) {
                return $guest->can($permissions);
            }
        }

        return false;
    }

    /**
     * Checks if model has at least one of the given permissions.
     *
     * @param array $permissions
     *
     * @return bool
     */
    public function canAtLeast($permissions)
    {
        if ($this->auth->check()) {
            return $this->getModels()->contains(function ($value, $key) use ($permissions) {
                return $value->canAtLeast($permissions);
            });
        } else {
            $guest = Role::whereSlug('guest')->first();

            if ($guest) {
                return $guest->canAtLeast($permissions);
            }
        }

        return false;
    }

    /**
     * Checks if model is assigned the given role.
     *
     * @param  string $slug
     * @return bool
     */
    public function is($role)
    {
        if ($this->auth->check()) {
            return $this->getModels()->contains(function ($value, $key) use ($role) {
                return $value->isRole($role);
            });
        } else {
            if ($role === 'guest') {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the logged in user has a (Spark native) role within the given or current team.
     *
     * @param  array $roles
     * @param  numeric $teamId
     * @return bool
     */
    public function userRoleOnTeam($roles, $teamId = null)
    {
        $user = auth()->user();
        $teamModel = Spark::teamModel();
        $team = $teamId ? $teamModel::find($teamId) : $user->currentTeam;
        $userRole = $team ? $user->roleOn($team) : "";

        return is_array(with($roles)) ? in_array($userRole, with($roles)) : with($roles) == $userRole;
    }

    /**
     * Gets the current user and/or their current team, only if their contain the CanUseRoles trait
     *
     * @return collection
     */
    private function getModels()
    {
        $userTraits = class_uses(Spark::userModel());
        $teamTraits = class_uses(Spark::teamModel());
        $currentTeam = $this->auth->user()->currentTeam;
        $models = [];

        if ($userTraits && in_array(CanHaveRoles::class, $userTraits)) {
            $models[] = $this->auth->user();
        }

        if ($teamTraits && in_array(CanHaveRoles::class, $teamTraits) && $currentTeam) {
            $models[] = $currentTeam;
        }

        return collect($models);
    }
}
