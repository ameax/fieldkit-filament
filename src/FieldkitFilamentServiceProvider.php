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
            ->hasTranslations()
            ->hasMigration('create_fieldkit_filament_table')
            ->hasCommand(FieldkitFilamentCommand::class);
    }

    public function packageRegistered(): void
    {
        // Explicitly load translations from the resources/lang directory
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'fieldkit-filament');

        // Also publish translations so they can be customized
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/fieldkit-filament'),
        ], 'fieldkit-filament-lang');
    }
}
