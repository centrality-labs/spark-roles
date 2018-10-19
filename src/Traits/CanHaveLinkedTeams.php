<?php

namespace CentralityLabs\SparkRoles\Traits;

use Laravel\Spark\Spark;

trait CanHaveLinkedTeams
{
    public function parents($roles = null, $canAssume = true)
    {
        return $this->search('parent', $role);
    }

    public function children($role = null)
    {
        return $this->search('child', $role);
    }

    private function search($col, $role = null)
    {
        $roleCache = collect([]);
        $combined = collect([]);

        $recurse = function ($role) use (&$recurse, $col, &$roleCache, &$combined) {
            if ($role) {
                if ($roleCache->has($role)) {
                    $roles = $roleCache->get($role);
                } else {
                    $roles = $this->canAssume($role);
                    $roleCache->push($role);
                }
            }

            $relation = $this->belongsToMany(Spark::teamModel(), 'team_link', $col, 'team_id');
            if ($roles) {
                $relation = $relation->wherePivotIn('role', $roles);
            }
            $teams = $relation->withPivot(['role'])->orderBy('name', 'asc')->get();

            foreach ($teams as $team) {
                if ($combined->has($team->id)) {
                    continue;
                }
                $combined->push($team->id, $team);
                $combined = $team->search($col, $team->pivot->role)->merge($combined);
            }
        };

        $recurse($role);

        return $combined->values();
    }

    private function canAssume($role)
    {
        $canAssume = config('sparkroles.teamlink.canassume');
        $assumed = collect([]);

        $recurse = function ($role) use (&$recurse, &$canAssume, &$assumed) {
            if ($assumed->contains($role)) {
                return;
            }
            $assumed->push($role);
            foreach ($canAssume[$role] as $assumes) {
                $assumed = $assumed->merge($recurse($assumes));
            }
        };

        $recurse($role);

        return $assumed;
    }
}
