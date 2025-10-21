<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages;

use Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFieldKitDefinition extends EditRecord
{
    protected static string $resource = FieldKitDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
