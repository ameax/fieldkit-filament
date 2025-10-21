<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages;

use Ameax\FieldkitFilament\Resources\FieldKitFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFieldKitForm extends EditRecord
{
    protected static string $resource = FieldKitFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
