<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources;

use Ameax\FieldkitCore\FieldKitInputRegistry;
use Ameax\FieldkitCore\Models\FieldKitDefinition;
use App\Filament\Resources\Resource;
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
    protected static ?string $model = FieldKitDefinition::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Definition')
                    ->tabs([
                        Tab::make('Basic Settings')
                            ->schema([
                                Section::make('Form & Context')
                                    ->schema([
                                        Select::make('fieldkit_form_id')
                                            ->label('Form')
                                            ->relationship('form', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        TextInput::make('field_key')
                                            ->autofocus()
                                            ->label('Field Key')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Unique identifier for this field (e.g., customer_phone)')
                                            ->placeholder('customer_phone'),
                                    ])
                                    ->columns(2),

                                Section::make('Field Configuration')
                                    ->schema([
                                        Select::make('type')
                                            ->label('Field Type')
                                            ->options(fn () => app(FieldKitInputRegistry::class)->getOptionsForAdmin())
                                            ->required()
                                            ->disabled(fn (?FieldKitDefinition $record) => $record && $record->hasSubmittedData()
                                            )
                                            ->helperText(fn (?FieldKitDefinition $record) => $record && $record->hasSubmittedData()
                                                    ? 'Type cannot be changed - field has existing data'
                                                    : 'Select the field type'
                                            ),

                                        TextInput::make('label')
                                            ->label('Label')
                                            ->required()
                                            ->placeholder('Phone Number'),

                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(2)
                                            ->placeholder('Enter your phone number'),

                                        TextInput::make('placeholder')
                                            ->label('Placeholder')
                                            ->placeholder('+1 (555) 123-4567'),
                                    ])
                                    ->columns(2),

                                Section::make('Display Settings')
                                    ->schema([
                                        TextInput::make('sort_order')
                                            ->label('Sort Order')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Fields are displayed in ascending order'),

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
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['value'] ?? null),
                                    ])
                                    ->description('Add options for select and radio fields. Users will see the label but the value will be stored.'),
                            ])
                            ->visible(fn (Get $get) => in_array($get('type'), ['select', 'radio'])),

                        Tab::make('External Mappings')
                            ->schema([
                                Section::make('System Integrations')
                                    ->schema([
                                        Repeater::make('external_mappings')
                                            ->label('External Mappings')
                                            ->schema([
                                                Select::make('adapter_type')
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

                                                KeyValue::make('config')
                                                    ->label('Configuration')
                                                    ->keyLabel('Key')
                                                    ->valueLabel('Value')
                                                    ->helperText('Additional configuration for this mapping'),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('form.name')
                    ->label('Form')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('field_key')
                    ->label('Field Key')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('options_count')
                    ->label('Options')
                    ->counts('options')
                    ->sortable()
                    ->alignCenter()
                    ->visible(fn ($record) => in_array($record?->type, ['select', 'radio'])),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('fieldkit_form_id')
                    ->label('Form')
                    ->relationship('form', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label('Type')
                    ->options(fn () => app(FieldKitInputRegistry::class)->getOptionsForAdmin()),

                TernaryFilter::make('is_required')
                    ->label('Required'),

                TernaryFilter::make('is_active')
                    ->label('Active Status'),
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
