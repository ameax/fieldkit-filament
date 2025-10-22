<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources;

use Ameax\FieldkitCore\Models\FieldKitForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FieldKitFormResource extends Resource
{
    // @phpstan-ignore-next-line
    protected static ?string $model = FieldKitForm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): ?string
    {
        return __('fieldkit-filament::resources.forms.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('fieldkit-filament::resources.forms.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('fieldkit-filament::resources.forms.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fieldkit-filament::resources.forms.plural_label');
    }

    public static function getNavigationSort(): ?int
    {
        return 900;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('fieldkit-filament::resources.forms.sections.basic_information'))
                    ->schema([
                        TextInput::make('purpose_token')
                            ->label(__('fieldkit-filament::resources.forms.fields.purpose_token.label'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText(__('fieldkit-filament::resources.forms.fields.purpose_token.helper'))
                            ->placeholder(__('fieldkit-filament::resources.forms.fields.purpose_token.placeholder')),

                        TextInput::make('name')
                            ->label(__('fieldkit-filament::resources.forms.fields.name.label'))
                            ->required()
                            ->placeholder(__('fieldkit-filament::resources.forms.fields.name.placeholder'))
                            ->afterStateUpdated(function (string $operation, $state, callable $set) {
                                if ($operation !== 'create') {
                                    return;
                                }

                                $set('purpose_token', Str::slug($state, separator: '_'));
                            })
                            ->live(onBlur: true),

                        Textarea::make('description')
                            ->label(__('fieldkit-filament::resources.forms.fields.description.label'))
                            ->rows(3)
                            ->placeholder(__('fieldkit-filament::resources.forms.fields.description.placeholder')),

                        Toggle::make('is_active')
                            ->label(__('fieldkit-filament::resources.forms.fields.is_active.label'))
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make(__('fieldkit-filament::resources.forms.sections.multi_tenancy'))
                    ->schema([
                        TextInput::make('owner_type')
                            ->label(__('fieldkit-filament::resources.forms.fields.owner_type.label'))
                            ->placeholder(__('fieldkit-filament::resources.forms.fields.owner_type.placeholder')),

                        TextInput::make('owner_id')
                            ->label(__('fieldkit-filament::resources.forms.fields.owner_id.label'))
                            ->numeric(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('purpose_token')
                    ->label(__('fieldkit-filament::resources.forms.fields.purpose_token.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('fieldkit-filament::resources.forms.fields.name.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fields_count')
                    ->label(__('fieldkit-filament::resources.forms.fields.fields_count.label'))
                    ->counts('fields')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('fieldkit-filament::resources.forms.fields.is_active.label'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('fieldkit-filament::resources.forms.fields.created_at.label'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('fieldkit-filament::resources.forms.filters.is_active')),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            \Ameax\FieldkitFilament\Resources\FieldKitFormResource\RelationManagers\FieldDefinitionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages\ListFieldKitForms::route('/'),
            'create' => \Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages\CreateFieldKitForm::route('/create'),
            'edit' => \Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages\EditFieldKitForm::route('/{record}/edit'),
        ];
    }
}
