<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
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
                Section::make('Option Details')
                    ->schema([
                        TextInput::make('value')
                            ->label('Value')
                            ->required()
                            ->helperText('Stored in fieldkit_data'),

                        TextInput::make('label')
                            ->label('Label')
                            ->required()
                            ->helperText('Displayed to user'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->helperText('Optional - for radio buttons with descriptions'),

                        TextInput::make('external_identifier')
                            ->label('External ID')
                            ->helperText('Optional - ID for external system. Fallback: value'),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Options are displayed in ascending order'),
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
                    ->label('Value')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('external_identifier')
                    ->label('External ID')
                    ->toggleable()
                    ->placeholder('(uses value)'),

                TextColumn::make('sort_order')
                    ->label('Order')
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