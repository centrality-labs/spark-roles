<?php
namespace CentralityLabs\SparkRoles;

use Blade;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

use CentralityLabs\SparkRoles\SparkRoles;

class SparkRolesServiceProvider extends ServiceProvider
{
    /**
     * Indicates of loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the service provider
     *
     * @return null
     */
    public function boot()
    {
        $this->publish();
        $this->registerBladeDirectives();
    }

    /**
     * Register the service provider
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../install-stubs/config/sparkroles.php',
            'sparkroles'
        );

        $this->app->singleton(SparkRoles::class, function ($app) {
            $auth = $app->make('Illuminate\Contracts\Auth\Guard');

            return new SparkRoles($auth);
        });
    }

    /**
     * Construct the array of files to publish
     *
     * @return void
     */
    protected function publish()
    {
        $publishes = [];
        $date = Carbon::now();
        $stubs = __DIR__.'/../install-stubs';

        foreach ($this->getMigrations() as $key => $migration) {
            $exists = glob(database_path("/migrations/*_{$migration}.php"));
            $timestamp = $date->addSeconds($key)->format('Y_m_d_His');
            $filename = ($exists && count($exists) === 1) ? $exists[0] : database_path("migrations/{$timestamp}_{$migration}.php");
            $publishes["{$stubs}/database/migrations/{$migration}.php"] = $filename;
        }
        $publishes[realpath("{$stubs}/config")] = config_path();
        $publishes[realpath("{$stubs}/models")] = app_path();

        $this->publishes($publishes);
    }

    /**
     * Get the appropriate migration files in the correct order to be applied
     *
     * @return array
     */
    protected function getMigrations()
    {
        return [
            'create_roles_table',
            'create_permissions_table',
            'create_permission_role_table',
            'create_role_scopes_table',
            'create_team_link_table',
        ];
    }

    /**
     * Register the blade directives.
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        Blade::directive('can', function ($expression) {
            return "<?php if (\\SparkRoles::can({$expression})): ?>";
        });

        Blade::directive('endcan', function ($expression) {
            return "<?php endif; ?>";
        });

        Blade::directive('canatleast', function ($expression) {
            return "<?php if (\\SparkRoles::canAtLeast({$expression})): ?>";
        });

        Blade::directive('endcanatleast', function ($expression) {
            return "<?php endif; ?>";
        });

        Blade::directive('role', function ($expression) {
            return "<?php if (\\SparkRoles::is({$expression})): ?>";
        });

        Blade::directive('endrole', function ($expression) {
            return "<?php endif; ?>";
        });

        Blade::directive('roleonteam', function ($arguments) {
            list($roles, $team_id) = $this->getArguments($arguments, 2);
            return "<?php if(\\SparkRoles::userRoleOnTeam('{$roles}', '{$team_id}')) : ?>";
        });

        Blade::directive('endroleonteam', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('hascurrentteam', function () {
            return "<?php if(Auth::user()->currentTeam) : ?>";
        });

        Blade::directive('endhascurrentteam', function () {
            return "<?php endif; ?>";
        });
    }

    protected function getArguments($arguments, $count, $padding = null)
    {
        return array_pad(explode(',', str_replace(['(',')',' ', "'"], '', $arguments)), $count, $padding);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SparkRoles::class];
    }
}
