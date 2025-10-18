<?php

namespace Ameax\FieldkitFilament\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ameax\FieldkitFilament\FieldkitFilament
 */
class FieldkitFilament extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ameax\FieldkitFilament\FieldkitFilament::class;
    }
}
