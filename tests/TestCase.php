<?php

namespace Ameax\FieldkitFilament\Tests;

use Ameax\FieldkitFilament\FieldkitFilamentServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Ameax\\FieldkitCore\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            \Livewire\LivewireServiceProvider::class,
            \Filament\FilamentServiceProvider::class,
            \Filament\Forms\FormsServiceProvider::class,
            \Filament\Tables\TablesServiceProvider::class,
            \Ameax\FieldkitCore\FieldkitCoreServiceProvider::class,
            FieldkitFilamentServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Queue configuration for testing
        config()->set('queue.default', 'sync');

        // FieldKit configuration for testing
        config()->set('fieldkit.input_types', [
            'text' => \Ameax\FieldkitCore\Inputs\FieldKitTextInput::class,
            'email' => \Ameax\FieldkitCore\Inputs\FieldKitEmailInput::class,
            'number' => \Ameax\FieldkitCore\Inputs\FieldKitNumberInput::class,
            'textarea' => \Ameax\FieldkitCore\Inputs\FieldKitTextareaInput::class,
            'checkbox' => \Ameax\FieldkitCore\Inputs\FieldKitCheckboxInput::class,
            'select' => \Ameax\FieldkitCore\Inputs\FieldKitSelectInput::class,
            'radio' => \Ameax\FieldkitCore\Inputs\FieldKitRadioInput::class,
        ]);

        config()->set('fieldkit.definition_sources', [
            'config' => ['priority' => 200],
            'database' => ['priority' => 100],
            'json' => [
                'priority' => 50,
                'path' => storage_path('fieldkit'),
            ],
        ]);

        config()->set('fieldkit.handlers', []);

        $this->loadMigrationsFrom(__DIR__.'/../../../fieldkit-core/database/migrations');

        // Load main app migrations for User model if needed
        if (file_exists(__DIR__.'/../../../../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../../../../database/migrations');
        }
    }
}
