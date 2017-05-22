<?php

return array(
    /*
    |--------------------------------------------------------------------------
    | User models
    |--------------------------------------------------------------------------
    |
    | Specify the local model classes.
    |
    */
    'models'  => [
        'role' => 'App\\Role',
        'permission' => 'App\\Permission',
    ],

    /*
    |--------------------------------------------------------------------------
    | Spark User and Team Roles
    |--------------------------------------------------------------------------
    |
    | Specify Spark roles and provide the role hierarchy
    |
    */
    'teamlink'  => [
        'roles' => [
            'owner' => 'Owner',
            'admin' => 'Administrator',
            'observe' => 'Observer',
        ],
        'canassume' => [
            'owner' => ['admin'],
            'admin' => ['manage'],
            'manager' => ['observe'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Developer
    |--------------------------------------------------------------------------
    |
    | Add all users, or all users in a team, with a given role to the Spark
    | developer array. Caching of developer array can be disabled by setting
    | 'cache' to false.
    |
    */
    'developer'  => [
        'enable' => true,
        'slug' => 'developer',
        'cache' => [
            'key' => 'spark.developers',
            'timeout' => 12 * 60 // 12 hours in minutes
        ]
    ],
);
