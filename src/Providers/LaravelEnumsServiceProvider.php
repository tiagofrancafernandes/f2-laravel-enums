<?php

namespace TiagoF2\Enums\Providers;

use Illuminate\Support\ServiceProvider;
use TiagoF2\Enums\Console\Commands\MakeEnumCommand;
use TiagoF2\Enums\Support\Package;
use TiagoF2\Enums\Support\Stub;

class LaravelEnumsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $configPath = Package::packageBasePath('config/f2-enums.php');

        $publishPath = function_exists('config_path')
            ? config_path('f2-enums.php')
            : base_path('config/f2-enums.php');

        $this->publishes([
            $configPath => $publishPath,
        ], 'f2-enums-config');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $configPath = Package::packageBasePath('config/f2-enums.php');
        $this->mergeConfigFrom($configPath, 'f2-enums-config');

        $this->app->singleton('command.f2-enums.make-enum', fn () => new MakeEnumCommand());

        $this->commands(
            'command.f2-enums.make-enum',
        );

        $stubsPath = Stub::getStubsPathOnPackagePath();

        $this->publishes([
            $stubsPath => Stub::getStubsPathOnApp(),
        ], 'f2-enums-stubs');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.f2-enums.make-enum',
        ];
    }
}
