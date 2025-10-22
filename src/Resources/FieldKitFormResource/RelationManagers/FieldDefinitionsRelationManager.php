<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources\FieldKitFormResource\RelationManagers;

use Ameax\FieldkitCore\FieldKitInputRegistry;
use App\Helpers\ArrayHelper;
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
use Illuminate\Support\Str;

class FieldDefinitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    protected static ?string $title = 'Field Definitions';

    protected ?string $tempQuickOptions = null;

    protected function processQuickOptions($record, array $data): void
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
                Tabs::make('Field Definition')
                    ->tabs([
                        Tab::make('Basic Settings')
                            ->schema([
                                Section::make('Field Configuration')
                                    ->schema([
                                        TextInput::make('key')
                                            ->label('Field Key')
                                            ->required()
                                            ->unique(
                                                table: 'fieldkit_definitions',
                                                column: 'key',
                                                ignoreRecord: true,
                                                modifyRuleUsing: function ($rule) {
                                                    return $rule->where('fieldkit_form_id', $this->getOwnerRecord()->id);
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
                                            ->helperText('Unique identifier for this field (e.g., customer_phone)')
                                            ->placeholder('customer_phone'),

                                        Select::make('type')
                                            ->label('Field Type')
                                            ->options(fn () => app(FieldKitInputRegistry::class)->getOptionsForAdmin())
                                            ->required()
                                            ->live()
                                            ->helperText('Select the field type'),

                                        TextInput::make('label')
                                            ->label('Label')
                                            ->required(),

                                        TextInput::make('sort_order')
                                            ->label('Sort Order')
                                            ->numeric()
                                            ->default(function () {
                                                $maxSort = $this->getOwnerRecord()->fields()->max('sort_order') ?? 1;

                                                return $maxSort + 1;
                                            })
                                            ->helperText('Fields are displayed in ascending order'),

                                        Textarea::make('quick_options')
                                            ->label('Options')
                                            ->rows(5)
                                            ->columnSpanFull()
                                            ->helperText('Enter one option per line. Each will create an option with the label as display and slug as value.')
                                            ->visible(fn (Get $get, string $operation) => $operation === 'create' && in_array($get('type'), ['select', 'radio'])
                                            ),
                                    ])
                                    ->columns(2),

                                Section::make('Display Settings')
                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(2),

                                        TextInput::make('placeholder')
                                            ->label('Placeholder'),

                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make('Validation')
                            ->schema([
                                Section::make('Validation Rules')
                                    ->schema([
                                        Toggle::make('is_required')
                                            ->label('Required Field')
                                            ->default(false),

                                        TagsInput::make('validation_rules')
                                            ->label('Laravel Validation Rules')
                                            ->placeholder('Add validation rules...')
                                            ->helperText('Type and press Enter. Add parameters like: min:3, max:255, size:10, between:1,10')
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

                        Tab::make('Options')
                            ->schema([
                                Section::make('Field Options')
                                    ->schema([
                                        Repeater::make('options')
                                            ->label('Options')
                                            ->relationship()
                                            ->schema([
                                                TextInput::make('value')
                                                    ->label('Value')
                                                    ->required()
                                                    ->placeholder('yes')
                                                    ->helperText('The actual value stored when this option is selected'),

                                                TextInput::make('label')
                                                    ->label('Label')
                                                    ->required()
                                                    ->placeholder('Yes')
                                                    ->helperText('The text shown to users'),

                                                TextInput::make('description')
                                                    ->label('Description')
                                                    ->placeholder('Optional description')
                                                    ->helperText('Additional description text'),

                                                TextInput::make('icon')
                                                    ->label('Icon')
                                                    ->placeholder('heroicon-o-check')
                                                    ->helperText('Optional Heroicon name'),

                                                TextInput::make('external_identifier')
                                                    ->label('External ID')
                                                    ->placeholder('ext_123')
                                                    ->helperText('External system identifier'),

                                                TextInput::make('sort_order')
                                                    ->label('Sort Order')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->helperText('Display order (lower numbers first)'),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Option')
                                            ->reorderableWithButtons()
                                            ->collapsible()
                                            ->collapsed(true)
                                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['value'] ?? null),
                                    ])
                                    ->description('Add options for select and radio fields. Users will see the label but the value will be stored.'),
                            ])
                            ->visible(fn (Get $get) => in_array($get('type'), ['select', 'radio'])),

                        Tab::make('External Mappings')
                            ->schema([
                                Section::make('System Integrations')
                                    ->schema([
                                        Repeater::make('mappings')
                                            ->label('External Mappings')
                                            ->schema([
                                                Select::make('adapter')
                                                    ->label('Adapter Type')
                                                    ->options([
                                                        'ameax_column' => 'Ameax Database Column',
                                                        'mailchimp_api' => 'Mailchimp API',
                                                        'custom_webhook' => 'Custom Webhook',
                                                    ])
                                                    ->required(),

                                                TextInput::make('target')
                                                    ->label('Target')
                                                    ->required()
                                                    ->placeholder('customer.phone_number')
                                                    ->helperText('Dot notation path (e.g., customer.phone_number)'),

                                                KeyValue::make('transformations')
                                                    ->label('Transformations')
                                                    ->keyLabel('Key')
                                                    ->valueLabel('Value')
                                                    ->helperText('Additional transformations for this mapping'),

                                                KeyValue::make('conditions')
                                                    ->label('Conditions')
                                                    ->keyLabel('Key')
                                                    ->valueLabel('Value')
                                                    ->helperText('Conditions for this mapping'),
                                            ])
                                            ->columns(1)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Mapping')
                                            ->collapsible(),
                                    ]),
                            ]),

                        Tab::make('Conditional Visibility')
                            ->schema([
                                Section::make('Display Conditions')
                                    ->schema([
                                        Repeater::make('conditions')
                                            ->label('Visibility Conditions')
                                            ->schema([
                                                Select::make('field_type')
                                                    ->label('Field Type')
                                                    ->options([
                                                        'native' => 'Native Field',
                                                        'fieldkit' => 'FieldKit Field',
                                                    ])
                                                    ->required()
                                                    ->live(),

                                                TextInput::make('field_key')
                                                    ->label('Field Key')
                                                    ->required()
                                                    ->helperText('Key of the field this depends on'),

                                                Select::make('operator')
                                                    ->label('Operator')
                                                    ->options([
                                                        'in' => 'Value is in list',
                                                        'not_in' => 'Value is not in list',
                                                        'equals' => 'Value equals',
                                                        'not_equals' => 'Value does not equal',
                                                    ])
                                                    ->required()
                                                    ->live(onBlur: true),

                                                TextInput::make('expected_values')
                                                    ->label('Expected Value')
                                                    ->helperText('Single value that will show/hide this field')
                                                    ->visible(fn (Get $get) => in_array($get('operator'), ['equals', 'not_equals'])),

                                                TagsInput::make('expected_values')
                                                    ->label('Expected Values')
                                                    ->helperText('Values that will show/hide this field')
                                                    ->separator(',')
                                                    ->visible(fn (Get $get) => in_array($get('operator'), ['in', 'not_in'])),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Condition')
                                            ->collapsible(),
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('key')
                    ->label('Field Key')
                    ->searchable()
                    ->copyable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('label')
                    ->label('Display Label')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'text',
                        'success' => 'email',
                        'warning' => 'number',
                        'info' => 'select',
                        'secondary' => 'radio',
                        'danger' => 'checkbox',
                        'gray' => 'textarea',
                    ]),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('options_count')
                    ->label('Options')
                    ->counts('options')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Field Type')
                    ->options([
                        'text' => 'Text Input',
                        'email' => 'Email Input',
                        'number' => 'Number Input',
                        'textarea' => 'Textarea',
                        'select' => 'Select Dropdown',
                        'radio' => 'Radio Buttons',
                        'checkbox' => 'Checkbox',
                    ]),

                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Required Fields'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Fields'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->label('Add Field')
                    ->mutateFormDataUsing(function (array $data): array {
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
