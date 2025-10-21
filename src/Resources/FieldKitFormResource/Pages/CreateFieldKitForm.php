<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages;

use Ameax\FieldkitFilament\Resources\FieldKitFormResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFieldKitForm extends CreateRecord
{
    protected static string $resource = FieldKitFormResource::class;
}
