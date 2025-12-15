<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament;

use Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource;
use Ameax\FieldkitFilament\Resources\FieldKitFormResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FieldkitFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'fieldkit-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            FieldKitFormResource::class,
            FieldKitDefinitionResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
