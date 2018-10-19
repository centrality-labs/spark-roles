# CentralityLabs Spark Roles

[![Laravel 5.3](https://img.shields.io/badge/Laravel-5.3-orange.svg?style=flat-square)](http://laravel.com)
[![Spark 2.0](https://img.shields.io/badge/Spark-2.0-orange.svg?style=flat-square)](https://spark.laravel.com)
[![Source](http://img.shields.io/badge/source-centrality-labs/spark--roles-blue.svg?style=flat-square)](https://github.com/centrality-labs/spark-roles)
[![Build Status](https://travis-ci.org/CentralityLabs/spark-roles.svg?branch=master)](https://travis-ci.org/CentralityLabs/spark-roles)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

SparkRoles, based on [Caffeinated Shinobi](https://github.com/caffeinated/shinobi/), brings a simple and light-weight role-based permissions system to Laravel Spark's Team and User models through the following ACL structure:

- Every user can have zero or more roles.
- Every user can have zero or more permissions.
- Every team can have zero or more roles.
- Every team can have zero or more permissions.
- Roles and permissions can be shared between users and teams.
- Optionally, users and teams with a certain role (e.g. `developer`) are added to the Spark developer's array.

Permissions are then inherited to the team/user through the team/user's assigned roles.

This package is a replacement for [Caffeinated Shinobi](https://github.com/caffeinated/shinobi/) when building a project based on `Laravel/Spark`.

## Documentation

You will find user friendly documentation in the [CentralityLabs SparkRoles Wiki](https://github.com/centrality-labs/spark-roles/wiki) **BEING UPDATED**

## Quick Installation

1. Install the package through Composer.

    ```bash
    composer require centrality-labs/spark-roles:dev-master
    ```

1. Add the service provider to your project's `config/app.php` file.

    ```php
    CentralityLabs\SparkRoles\SparkRolesServiceProvider::class,
    ```

1. Publish the configuration, models, and migrations into your project.

    ```bash
    php artisan vendor:publish --provider="CentralityLabs\SparkRoles\SparkRolesServiceProvider"
    ```

1. Migrate your database.

    ```bash
    php artisan migrate
    ```

1. Add the `CanUseRoles` trait to your `Team` and/or `User` models, for example:

    - `app\Team.php`
    ```php
    <?php

    namespace App;

    use Laravel\Spark\Team as SparkTeam;
    use CentralityLabs\SparkRoles\Traits\CanHaveRoles;

    class Team extends SparkTeam
    {
        use CanHaveRoles;
        ...
    }
    ```

    - `app\User.php`
    ```php
    <?php

    namespace App;

    use Laravel\Spark\User as SparkUser;
    use CentralityLabs\SparkRoles\Traits\CanHaveRoles;

    class User extends SparkUser
    {
        use CanHaveRoles;
        ...
    }
    ```

1. **Optional:** If you'd like to dynamically assign the Spark developer array based on team/user roles then open `app\Http\kernel.php` and add the following to the `web` middleware group:

    ```php
    \CentralityLabs\SparkRoles\Middleware\AddDevelopers::class,
    ```

    This middleware can be controlled (enabled/disabled and choose the role slug that identifies developers) in the package's configuration file.

1. Start using roles for your Spark teams and users!

## Contributing

Fork, edit, pull request. You know the drill.

### Hit list

If you'd like to contribute consider helping with one of the following:

- [X] ~~Add Travis CI integration~~
- [X] ~~Add PHPUnit support~~
- [ ] Add meaningful unit tests
- [ ] Add a Kiosk based frontend for defining roles and permissions, similar to [Watchtower](https://github.com/SmarchSoftware/watchtower) for [Shinobi](https://github.com/caffeinated/shinobi/)

## Awesome Shinobi

[Caffeinated Shinobi](https://github.com/caffeinated/shinobi/) is an awesome tool for Laravel that adds Role Based Access Control (RBAC) to your user model. There is also a cool UI for Shinobi called [Watchtower](https://github.com/SmarchSoftware/watchtower). Go take a look!
