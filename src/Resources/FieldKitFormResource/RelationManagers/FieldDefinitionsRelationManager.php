<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources\FieldKitFormResource\RelationManagers;

use Ameax\FieldkitCore\Contracts\ContextProviderInterface;
use Ameax\FieldkitCore\FieldKitInputRegistry;
use Ameax\FieldkitFilament\Helpers\ArrayHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FieldDefinitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    protected static ?string $title = null;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('fieldkit-filament::resources.definitions.title');
    }

    protected ?string $tempQuickOptions = null;

    protected function processQuickOptions(mixed $record, array $data): void
    {
        if (! empty($data['quick_options']) && in_array($record->type, ['select', 'radio'])) {
            $lines = ArrayHelper::fromString($data['quick_options']);
            foreach ($lines as $index => $label) {
                if (! empty($label)) {
                    $record->options()->create([
                        'value' => Str::slug($label, separator: '_'),
                        'label' => $label,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('fieldkit-filament::resources.definitions.tabs.definition'))
                    ->tabs([
                        Tab::make(__('fieldkit-filament::resources.definitions.tabs.basic_settings'))
                            ->schema([
                                Section::make(__('fieldkit-filament::resources.definitions.sections.field_configuration'))
                                    ->schema([
                                        TextInput::make('key')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.key.label'))
                                            ->required()
                                            ->unique(
                                                table: 'fieldkit_definitions',
                                                column: 'key',
                                                ignoreRecord: true,
                                                modifyRuleUsing: function ($rule) {
                                                    return $rule->where('fieldkit_form_id', $this->getOwnerRecord()->getKey());
                                                }
                                            )
                                            ->afterStateUpdated(function (string $operation, $state, callable $set) {
                                                if ($operation !== 'create') {
                                                    return;
                                                }

                                                $set('key', Str::slug($state, separator: '_'));
                                            })
                                            ->live(onBlur: true)
                                            ->autofocus()
                                            ->helperText(__('fieldkit-filament::resources.definitions.fields.key.helper')),

                                        Select::make('type')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.type.label'))
                                            // @phpstan-ignore-next-line
                                            ->options(fn () => app(FieldKitInputRegistry::class)->getOptionsForAdmin())
                                            ->required()
                                            ->live()
                                            ->helperText(__('fieldkit-filament::resources.definitions.fields.type.helper')),

                                        TextInput::make('label')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.label.label'))
                                            ->required(),

                                        TextInput::make('sort_order')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.sort_order.label'))
                                            ->numeric()
                                            ->default(function () {
                                                $ownerRecord = $this->getOwnerRecord();
                                                if (method_exists($ownerRecord, 'fields')) {
                                                    $maxSort = $ownerRecord->fields()->max('sort_order') ?? 1;

                                                    return $maxSort + 1;
                                                }

                                                return 1;
                                            })
                                            ->helperText(__('fieldkit-filament::resources.definitions.fields.sort_order.helper')),

                                        Textarea::make('quick_options')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.options.quick_label'))
                                            ->rows(5)
                                            ->columnSpanFull()
                                            ->helperText(__('fieldkit-filament::resources.definitions.fields.options.quick_helper'))
                                            ->visible(fn (Get $get, string $operation) => $operation === 'create' && in_array($get('type'), ['select', 'radio'])),
                                    ])
                                    ->columns(2),

                                Section::make(__('fieldkit-filament::resources.definitions.sections.display_settings'))
                                    ->schema([
                                        Textarea::make('description')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.description.label'))
                                            ->rows(2),

                                        TextInput::make('placeholder')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.placeholder.label')),

                                        Toggle::make('is_active')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.is_active.label'))
                                            ->default(true),
                                    ])
                                    ->columns(2),

                                ...static::getContextSection(),
                            ]),

                        Tab::make(__('fieldkit-filament::resources.definitions.tabs.validation'))
                            ->schema([
                                Section::make(__('fieldkit-filament::resources.definitions.sections.validation_rules'))
                                    ->schema([
                                        Toggle::make('is_required')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.is_required.label'))
                                            ->default(false),

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
                                            ->collapsed(true)
                                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['value'] ?? null),
                                    ])
                                    ->description(__('fieldkit-filament::resources.definitions.fields.options.description')),
                            ])
                            ->visible(fn (Get $get) => in_array($get('type'), ['select', 'radio'])),

                        Tab::make(__('fieldkit-filament::resources.definitions.tabs.external_mappings'))
                            ->schema([
                                Section::make(__('fieldkit-filament::resources.definitions.sections.system_integrations'))
                                    ->schema([
                                        Repeater::make('mappings')
                                            ->label(__('fieldkit-filament::resources.definitions.fields.mappings.label'))
                                            ->schema([
                                                Select::make('adapter')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.adapter.label'))
                                                    ->options([
                                                        'ameax_column' => __('fieldkit-filament::resources.definitions.fields.adapter.options.ameax_column'),
                                                        'mailchimp_api' => __('fieldkit-filament::resources.definitions.fields.adapter.options.mailchimp_api'),
                                                        'custom_webhook' => __('fieldkit-filament::resources.definitions.fields.adapter.options.custom_webhook'),
                                                    ])
                                                    ->required(),

                                                TextInput::make('target')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.target.label'))
                                                    ->required()
                                                    ->placeholder(__('fieldkit-filament::resources.definitions.fields.target.placeholder'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.target.helper')),

                                                KeyValue::make('transformations')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.transformations.label'))
                                                    ->keyLabel(__('fieldkit-filament::resources.definitions.fields.transformations.key_label'))
                                                    ->valueLabel(__('fieldkit-filament::resources.definitions.fields.transformations.value_label'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.transformations.helper')),

                                                KeyValue::make('conditions')
                                                    ->label(__('fieldkit-filament::resources.definitions.fields.conditions.conditions_label'))
                                                    ->keyLabel(__('fieldkit-filament::resources.definitions.fields.conditions.key_label'))
                                                    ->valueLabel(__('fieldkit-filament::resources.definitions.fields.conditions.value_label'))
                                                    ->helperText(__('fieldkit-filament::resources.definitions.fields.conditions.helper')),
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

    /**
     * Get context section for field-level visibility (in Basic Settings tab)
     *
     * @return array<Section>
     */
    protected static function getContextSection(): array
    {
        if (! config('fieldkit.context.enabled', false)) {
            return [];
        }

        $providerClass = config('fieldkit.context.provider');

        if (! $providerClass || ! class_exists($providerClass)) {
            return [];
        }

        /** @var ContextProviderInterface $provider */
        $provider = app($providerClass);
        $fields = $provider->getFormFields();

        if (empty($fields)) {
            return [];
        }

        return [
            Section::make(__('fieldkit-filament::resources.definitions.sections.visibility'))
                ->description(__('fieldkit-filament::resources.definitions.sections.field_visibility_description'))
                ->schema($fields)
                ->columns(2)
                ->collapsible()
                ->collapsed(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('fieldkit-filament::resources.definitions.fields.sort_order.short'))
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('key')
                    ->label(__('fieldkit-filament::resources.definitions.fields.key.label'))
                    ->searchable()
                    ->copyable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('label')
                    ->label(__('fieldkit-filament::resources.definitions.fields.label.display_label'))
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('fieldkit-filament::resources.definitions.fields.type.label'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'primary',
                        'email' => 'success',
                        'number' => 'warning',
                        'select' => 'info',
                        'radio' => 'secondary',
                        'checkbox' => 'danger',
                        'textarea' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_required')
                    ->label(__('fieldkit-filament::resources.definitions.fields.is_required.short'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('fieldkit-filament::resources.definitions.fields.is_active.label'))
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('options_count')
                    ->label(__('fieldkit-filament::resources.definitions.fields.options_count.label'))
                    ->counts('options')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('fieldkit-filament::resources.definitions.filters.field_type'))
                    ->options([
                        'text' => __('fieldkit-filament::resources.definitions.fields.type.options.text'),
                        'email' => __('fieldkit-filament::resources.definitions.fields.type.options.email'),
                        'number' => __('fieldkit-filament::resources.definitions.fields.type.options.number'),
                        'textarea' => __('fieldkit-filament::resources.definitions.fields.type.options.textarea'),
                        'select' => __('fieldkit-filament::resources.definitions.fields.type.options.select'),
                        'radio' => __('fieldkit-filament::resources.definitions.fields.type.options.radio'),
                        'checkbox' => __('fieldkit-filament::resources.definitions.fields.type.options.checkbox'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_required')
                    ->label(__('fieldkit-filament::resources.definitions.filters.required_fields')),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('fieldkit-filament::resources.definitions.filters.active_fields')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->label(__('fieldkit-filament::resources.definitions.actions.add_field'))
                    ->mutateDataUsing(function (array $data): array {
                        // Store quick_options for later processing but remove from data to save
                        $this->tempQuickOptions = $data['quick_options'] ?? null;
                        unset($data['quick_options']);

                        return $data;
                    })
                    ->after(function ($record) {
                        // Process the quick options after record creation
                        if ($this->tempQuickOptions) {
                            $this->processQuickOptions($record, ['quick_options' => $this->tempQuickOptions]);
                            $this->tempQuickOptions = null;
                        }
                    }),
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }
}
