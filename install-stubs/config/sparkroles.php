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
    | developer array
    |
    */
    'developer'  => [
        'enable' => true,
        'slug' => 'developer',
    ],
);
