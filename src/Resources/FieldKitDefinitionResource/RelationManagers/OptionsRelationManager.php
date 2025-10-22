<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $recordTitleAttribute = 'label';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('fieldkit-filament::resources.options_relation_manager.section.option_details'))
                    ->schema([
                        TextInput::make('value')
                            ->label(__('fieldkit-filament::resources.options_relation_manager.fields.value.label'))
                            ->required()
                            ->helperText(__('fieldkit-filament::resources.options_relation_manager.fields.value.helper')),

                        TextInput::make('label')
                            ->label(__('fieldkit-filament::resources.options_relation_manager.fields.label.label'))
                            ->required()
                            ->helperText(__('fieldkit-filament::resources.options_relation_manager.fields.label.helper')),

                        Textarea::make('description')
                            ->label(__('fieldkit-filament::resources.options_relation_manager.fields.description.label'))
                            ->rows(2)
                            ->helperText(__('fieldkit-filament::resources.options_relation_manager.fields.description.helper')),

                        TextInput::make('external_identifier')
                            ->label(__('fieldkit-filament::resources.options_relation_manager.fields.external_identifier.label'))
                            ->helperText(__('fieldkit-filament::resources.options_relation_manager.fields.external_identifier.helper')),

                        TextInput::make('sort_order')
                            ->label(__('fieldkit-filament::resources.options_relation_manager.fields.sort_order.label'))
                            ->numeric()
                            ->default(0)
                            ->helperText(__('fieldkit-filament::resources.options_relation_manager.fields.sort_order.helper')),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('value')
                    ->label(__('fieldkit-filament::resources.options_relation_manager.fields.value.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('label')
                    ->label(__('fieldkit-filament::resources.options_relation_manager.fields.label.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('fieldkit-filament::resources.options_relation_manager.fields.description.label'))
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('external_identifier')
                    ->label(__('fieldkit-filament::resources.options_relation_manager.fields.external_identifier.label'))
                    ->toggleable()
                    ->placeholder(__('fieldkit-filament::resources.options_relation_manager.fields.external_identifier.placeholder')),

                TextColumn::make('sort_order')
                    ->label(__('fieldkit-filament::resources.options_relation_manager.fields.sort_order.short_label'))
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
