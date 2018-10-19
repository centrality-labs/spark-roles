<?php
namespace CentralityLabs\SparkRoles\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Laravel\Spark\Spark;
use CentralityLabs\SparkRoles\SparkRoles;

class Role extends Model
{
    /**
     * The attributes that are fillable via mass assignment.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description', 'special'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Static getter
     **/
    public static function get($roleIdSlugOrModel)
    {
        $role = null;
        if (is_string($roleIdSlugOrModel)) {
            $role = self::where('slug', $roleIdSlugOrModel)->first();
        }
        if (is_numeric($roleIdSlugOrModel)) {
            $role = self::find($roleIdSlugOrModel);
        }
        if (is_object($roleIdSlugOrModel)) {
            $role = $roleIdSlugOrModel;
        }
        return $role;
    }

    /**
     * Get all users assigned to this role.
     *
     * @return Model
     */
    public function users()
    {
        return $this->morphedByMany(Spark::userModel(), 'role_scope');
    }

    /**
     * Get all teams assigned to this role.
     *
     * @return Model
     */
    public function teams()
    {
        return $this->morphedByMany(Spark::teamModel(), 'role_scope');
    }

    /**
     * Roles can have many permissions.
     *
     * @return Model
     */
    public function permissions()
    {
        return $this->belongsToMany(SparkRoles::permissionModel())->withTimestamps();
    }

    /**
     * Get permission slugs assigned to role.
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions->pluck('slug')->all();
    }

    /**
     * Checks if the role has the given permission.
     *
     * @param  string $permission
     * @return bool
     */
    public function can($permission)
    {
        if ($this->special === 'no-access') {
            return false;
        }

        if ($this->special === 'all-access') {
            return true;
        }

        $permissions = $this->getPermissions();

        if (is_array($permission)) {
            $permissionCount    = count($permission);
            $intersection       = array_intersect($permissions, $permission);
            $intersectionCount  = count($intersection);

            return ($permissionCount == $intersectionCount) ? true : false;
        } else {
            return in_array($permission, $permissions);
        }
    }

    /**
     * Check if the role has at least one of the given permissions
     *
     * @param  array $permission
     * @return bool
     */
    public function canAtLeast(array $permission = array())
    {
        if ($this->special === 'no-access') {
            return false;
        }

        if ($this->special === 'all-access') {
            return true;
        }

        $permissions = $this->getPermissions();

        $intersection       = array_intersect($permissions, $permission);
        $intersectionCount  = count($intersection);

        return ($intersectionCount > 0) ? true : false;
    }

    /**
     * Assigns the given permission to the role.
     *
     * @param  int $permissionId
     * @return bool
     */
    public function assignPermission($permissionId = null)
    {
        $permissions = $this->permissions;

        if (! $permissions->contains($permissionId)) {
            return $this->permissions()->attach($permissionId);
        }

        return false;
    }

    /**
     * Revokes the given permission from the role.
     *
     * @param  int $permissionId
     * @return bool
     */
    public function revokePermission($permissionId = '')
    {
        return $this->permissions()->detach($permissionId);
    }

    /**
     * Syncs the given permission(s) with the role.
     *
     * @param  array $permissionIds
     * @return bool
     */
    public function syncPermissions(array $permissionIds = array())
    {
        return $this->permissions()->sync($permissionIds);
    }

    /**
     * Revokes all permissions from the role.
     *
     * @return bool
     */
    public function revokeAllPermissions()
    {
        return $this->permissions()->detach();
    }
}
