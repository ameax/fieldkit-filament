<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages;

use Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFieldKitDefinition extends CreateRecord
{
    protected static string $resource = FieldKitDefinitionResource::class;
}