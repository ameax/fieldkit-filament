<?php

namespace Ameax\FieldkitFilament;

use Ameax\FieldkitFilament\Commands\FieldkitFilamentCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FieldkitFilamentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('fieldkit-filament')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_fieldkit_filament_table')
            ->hasCommand(FieldkitFilamentCommand::class);
    }
}
