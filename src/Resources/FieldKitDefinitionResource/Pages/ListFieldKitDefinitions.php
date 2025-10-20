<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages;

use Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFieldKitDefinitions extends ListRecords
{
    protected static string $resource = FieldKitDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}