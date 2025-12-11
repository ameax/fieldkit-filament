<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources;

use Ameax\FieldkitCore\FieldKitInputRegistry;
use Ameax\FieldkitCore\Models\FieldKitDefinition;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FieldKitDefinitionResource extends Resource
{
    // @phpstan-ignore-next-line
    protected static ?string $model = FieldKitDefinition::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('fieldkit-filament::resources.definitions.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('fieldkit-filament::resources.definitions.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fieldkit-filament::resources.definitions.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('fieldkit-filament::resources.definitions.tabs.definition'))
                    ->tabs([
                        Tab::make(__('fieldkit-filament::resources.definitions.tabs.basic_settings'))
                            ->schema([
                                Section::make(__('fieldkit-filament::resources.definitions.sections.form_context'))
                                    ->schema([
                                        Select::make('fieldkit_form_id')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.fieldkit_form_id.label'))
                                            ->relationship('form', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        TextInput::make('field_key')
                                            ->autofocus()
                                            ->label(__('fieldkit-filament::resources.definitions.fields.field_key.label'))
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText(__('fieldkit-filament::resources.definitions.fields.field_key.helper'))
                                            ->placeholder(__('fieldkit-filament::resources.definitions.fields.field_key.placeholder')),
                                    ])
                                    ->columns(2),

                                Section::make(__('fieldkit-filament::resources.definitions.sections.field_configuration'))
                                    ->schema([
                                        Select::make('type')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.type.label'))
                                            // @phpstan-ignore-next-line
                                            ->options(fn () => app(FieldKitInputRegistry::class)->getOptionsForAdmin())
                                            ->required()
                                            // @phpstan-ignore-next-line
                                            ->disabled(fn (?FieldKitDefinition $record) => $record && $record->hasSubmittedData()
                                            )
                                            // @phpstan-ignore-next-line
                                            ->helperText(fn (?FieldKitDefinition $record) => $record && $record->hasSubmittedData()
                                                    ? __('fieldkit-filament::resources.definitions.fields.type.helper_disabled')
                                                    : __('fieldkit-filament::resources.definitions.fields.type.helper')
                                            ),

                                        TextInput::make('label')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.label.label'))
                                            ->required()
                                            ->placeholder(__('fieldkit-filament::resources.definitions.fields.label.placeholder')),

                                        Textarea::make('description')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.description.label'))
                                            ->rows(2)
                                            ->placeholder(__('fieldkit-filament::resources.definitions.fields.description.placeholder')),

                                        TextInput::make('placeholder')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.placeholder.label'))
                                            ->placeholder(__('fieldkit-filament::resources.definitions.fields.placeholder.placeholder')),
                                    ])
                                    ->columns(2),

                                Section::make(__('fieldkit-filament::resources.definitions.sections.display_settings'))
                                    ->schema([
                                        TextInput::make('sort_order')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.sort_order.label'))
                                            ->numeric()
                                            ->default(0)
                                            ->helperText(__('fieldkit-filament::resources.definitions.fields.sort_order.helper')),

                                        Toggle::make('is_active')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.is_active.label'))
                                            ->default(true),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(__('fieldkit-filament::resources.definitions.tabs.validation'))
                            ->schema([
                                Section::make(__('fieldkit-filament::resources.definitions.sections.validation_rules'))
                                    ->schema([
                                        Toggle::make('is_required')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.is_required.label'))
                                            ->default(false),

                                        Select::make('validation_pattern')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.validation_pattern.label'))
                                            ->placeholder(__('fieldkit-filament::resources.definitions.fields.validation_pattern.placeholder'))
                                            ->options(\Ameax\FieldkitCore\Enums\ValidationPatternEnum::options()),

                                        TagsInput::make('validation_rules')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.validation_rules.label'))
                                            ->placeholder(__('fieldkit-filament::resources.definitions.fields.validation_rules.placeholder'))
                                            ->helperText(__('fieldkit-filament::resources.definitions.fields.validation_rules.helper'))
                                            ->separator('|')
                                            ->suggestions([
                                                'accepted',
                                                'active_url',
                                                'alpha',
                                                'alpha_dash',
                                                'alpha_num',
                                                'array',
                                                'bail',
                                                'boolean',
                                                'confirmed',
                                                'declined',
                                                'digits',
                                                'distinct',
                                                'email',
                                                'filled',
                                                'integer',
                                                'ip',
                                                'json',
                                                'lowercase',
                                                'nullable',
                                                'numeric',
                                                'password',
                                                'present',
                                                'prohibited',
                                                'sometimes',
                                                'string',
                                                'timezone',
                                                'uppercase',
                                                'url',
                                                'uuid',
                                            ]),

                                    ])
                                    ->columns(2),

                            ]),

                        Tab::make(__('fieldkit-filament::resources.definitions.tabs.options'))
                            ->schema([
                                Section::make(__('fieldkit-filament::resources.definitions.sections.field_options'))
                                    ->schema([
                                        Repeater::make('options')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.options.label'))
                                            ->relationship()
                                            ->schema([
                                                TextInput::make('value')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.options.fields.value.label'))
                                                    ->required()
                                                    ->placeholder(__('fieldkit-filament::resources.definitions.fields.options.fields.value.placeholder'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.options.fields.value.helper')),

                                                TextInput::make('label')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.options.fields.label.label'))
                                                    ->required()
                                                    ->placeholder(__('fieldkit-filament::resources.definitions.fields.options.fields.label.placeholder'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.options.fields.label.helper')),

                                                TextInput::make('description')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.options.fields.description.label'))
                                                    ->placeholder(__('fieldkit-filament::resources.definitions.fields.options.fields.description.placeholder'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.options.fields.description.helper')),

                                                TextInput::make('icon')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.options.fields.icon.label'))
                                                    ->placeholder(__('fieldkit-filament::resources.definitions.fields.options.fields.icon.placeholder'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.options.fields.icon.helper')),

                                                TextInput::make('external_identifier')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.options.fields.external_identifier.label'))
                                                    ->placeholder(__('fieldkit-filament::resources.definitions.fields.options.fields.external_identifier.placeholder'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.options.fields.external_identifier.helper')),

                                                TextInput::make('sort_order')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.options.fields.sort_order.label'))
                                                    ->numeric()
                                                    ->default(1)
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.options.fields.sort_order.helper')),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel(__('fieldkit-filament::resources.definitions.actions.add_option'))
                                            ->reorderableWithButtons()
                                            ->collapsible()
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['value'] ?? null),
                                    ])
                                    ->description(__('fieldkit-filament::resources.definitions.fields.options.description')),
                            ])
                            ->visible(fn (Get $get) => in_array($get('type'), ['select', 'radio'])),

                        Tab::make(__('fieldkit-filament::resources.definitions.tabs.external_mappings'))
                            ->schema([
                                Section::make(__('fieldkit-filament::resources.definitions.sections.system_integrations'))
                                    ->schema([
                                        Repeater::make('external_mappings')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.external_mappings.label'))
                                            ->schema([
                                                Select::make('adapter_type')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.adapter_type.label'))
                                                    ->options([
                                                        'ameax_column' => __('fieldkit-filament::resources.definitions.fields.adapter_type.options.ameax_column'),
                                                        'mailchimp_api' => __('fieldkit-filament::resources.definitions.fields.adapter_type.options.mailchimp_api'),
                                                        'custom_webhook' => __('fieldkit-filament::resources.definitions.fields.adapter_type.options.custom_webhook'),
                                                    ])
                                                    ->required(),

                                                TextInput::make('target')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.target.label'))
                                                    ->required()
                                                    ->placeholder(__('fieldkit-filament::resources.definitions.fields.target.placeholder'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.target.helper')),

                                                KeyValue::make('config')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.config.label'))
                                                    ->keyLabel(__('fieldkit-filament::resources.definitions.fields.config.key_label'))
                                                    ->valueLabel(__('fieldkit-filament::resources.definitions.fields.config.value_label'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.config.helper')),
                                            ])
                                            ->columns(1)
                                            ->defaultItems(0)
                                            ->addActionLabel(__('fieldkit-filament::resources.definitions.actions.add_mapping'))
                                            ->collapsible(),
                                    ]),
                            ]),

                        Tab::make(__('fieldkit-filament::resources.definitions.tabs.conditional_visibility'))
                            ->schema([
                                Section::make(__('fieldkit-filament::resources.definitions.sections.display_conditions'))
                                    ->schema([
                                        Repeater::make('conditions')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.conditions.label'))
                                            ->schema([
                                                Select::make('field_type')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.field_type.label'))
                                                    ->options([
                                                        'native' => __('fieldkit-filament::resources.definitions.fields.field_type.options.native'),
                                                        'fieldkit' => __('fieldkit-filament::resources.definitions.fields.field_type.options.fieldkit'),
                                                    ])
                                                    ->required()
                                                    ->live(),

                                                TextInput::make('field_key')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.field_key.label'))
                                                    ->required()
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.field_key.helper')),

                                                Select::make('operator')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.operator.label'))
                                                    ->options([
                                                        'in' => __('fieldkit-filament::resources.definitions.fields.operator.options.in'),
                                                        'not_in' => __('fieldkit-filament::resources.definitions.fields.operator.options.not_in'),
                                                        'equals' => __('fieldkit-filament::resources.definitions.fields.operator.options.equals'),
                                                        'not_equals' => __('fieldkit-filament::resources.definitions.fields.operator.options.not_equals'),
                                                    ])
                                                    ->required()
                                                    ->live(onBlur: true),

                                                TextInput::make('expected_values')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.expected_values.label'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.expected_values.helper'))
                                                    ->visible(fn (Get $get) => in_array($get('operator'), ['equals', 'not_equals'])),

                                                TagsInput::make('expected_values')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.expected_values.label_plural'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.expected_values.helper_plural'))
                                                    ->separator(',')
                                                    ->visible(fn (Get $get) => in_array($get('operator'), ['in', 'not_in'])),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel(__('fieldkit-filament::resources.definitions.actions.add_condition'))
                                            ->collapsible(),
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('form.name')
                    ->label(__('fieldkit-filament::resources.definitions.fields.form.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('field_key')
                    ->label(__('fieldkit-filament::resources.definitions.fields.field_key.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('fieldkit-filament::resources.definitions.fields.type.label'))
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('label')
                    ->label(__('fieldkit-filament::resources.definitions.fields.label.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('fieldkit-filament::resources.definitions.fields.sort_order.label'))
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make('is_required')
                    ->label(__('fieldkit-filament::resources.definitions.fields.is_required.short'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('fieldkit-filament::resources.definitions.fields.is_active.label'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('options_count')
                    ->label(__('fieldkit-filament::resources.definitions.fields.options_count.label'))
                    ->counts('options')
                    ->sortable()
                    ->alignCenter()
                    ->visible(fn ($record) => in_array($record?->type, ['select', 'radio'])),

                TextColumn::make('created_at')
                    ->label(__('fieldkit-filament::resources.definitions.fields.created_at.label'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('fieldkit_form_id')
                    ->label(__('fieldkit-filament::resources.definitions.filters.fieldkit_form_id'))
                    ->relationship('form', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label(__('fieldkit-filament::resources.definitions.filters.type'))
                    // @phpstan-ignore-next-line
                    ->options(fn () => app(FieldKitInputRegistry::class)->getOptionsForAdmin()),

                TernaryFilter::make('is_required')
                    ->label(__('fieldkit-filament::resources.definitions.filters.is_required')),

                TernaryFilter::make('is_active')
                    ->label(__('fieldkit-filament::resources.definitions.filters.is_active')),
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
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            // Options are now managed directly in the form using a Repeater
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages\ListFieldKitDefinitions::route('/'),
            'create' => \Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages\CreateFieldKitDefinition::route('/create'),
            'edit' => \Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages\EditFieldKitDefinition::route('/{record}/edit'),
        ];
    }
}
