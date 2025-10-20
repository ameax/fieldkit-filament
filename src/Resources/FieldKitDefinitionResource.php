<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources;

use Ameax\FieldkitCore\Models\FieldKitDefinition;
use Ameax\FieldkitCore\Models\FieldKitForm;
use Ameax\FieldkitCore\FieldKitInputRegistry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FieldKitDefinitionResource extends Resource
{
    protected static ?string $model = FieldKitDefinition::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Field Definitions';

    protected static ?string $navigationGroup = 'FieldKit';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Definition')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Settings')
                            ->schema([
                                Forms\Components\Section::make('Form & Context')
                                    ->schema([
                                        Forms\Components\Select::make('fieldkit_form_id')
                                            ->label('Form')
                                            ->relationship('form', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        Forms\Components\TextInput::make('field_key')
                                            ->label('Field Key')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Unique identifier for this field (e.g., customer_phone)')
                                            ->placeholder('customer_phone'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Field Configuration')
                                    ->schema([
                                        Forms\Components\Select::make('type')
                                            ->label('Field Type')
                                            ->options(fn() => app(FieldKitInputRegistry::class)->getOptionsForAdmin())
                                            ->required()
                                            ->disabled(fn(?FieldKitDefinition $record) =>
                                                $record && $record->hasSubmittedData()
                                            )
                                            ->helperText(fn(?FieldKitDefinition $record) =>
                                                $record && $record->hasSubmittedData()
                                                    ? 'Type cannot be changed - field has existing data'
                                                    : 'Select the field type'
                                            ),

                                        Forms\Components\TextInput::make('label')
                                            ->label('Label')
                                            ->required()
                                            ->placeholder('Phone Number'),

                                        Forms\Components\Textarea::make('description')
                                            ->label('Description')
                                            ->rows(2)
                                            ->placeholder('Enter your phone number'),

                                        Forms\Components\TextInput::make('placeholder')
                                            ->label('Placeholder')
                                            ->placeholder('+1 (555) 123-4567'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Display Settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Sort Order')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Fields are displayed in ascending order'),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Validation')
                            ->schema([
                                Forms\Components\Section::make('Validation Rules')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_required')
                                            ->label('Required Field')
                                            ->default(false),

                                        Forms\Components\TextInput::make('validation_rules')
                                            ->label('Laravel Validation Rules')
                                            ->placeholder('max:255|alpha_dash')
                                            ->helperText('Laravel validation syntax (e.g., max:255|alpha_dash)'),

                                        Forms\Components\TextInput::make('min_length')
                                            ->label('Minimum Length')
                                            ->numeric()
                                            ->minValue(0),

                                        Forms\Components\TextInput::make('max_length')
                                            ->label('Maximum Length')
                                            ->numeric()
                                            ->minValue(1),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Custom Validation Messages')
                                    ->schema([
                                        Forms\Components\KeyValue::make('validation_messages')
                                            ->label('Custom Error Messages')
                                            ->keyLabel('Rule')
                                            ->valueLabel('Message')
                                            ->helperText('Override default validation messages (e.g., required => "This field is mandatory")')
                                            ->addActionLabel('Add Message'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Options')
                            ->schema([
                                Forms\Components\Section::make('Field Options')
                                    ->schema([
                                        Forms\Components\Placeholder::make('options_info')
                                            ->label('')
                                            ->content('Options are managed through the Options relation manager below this form.'),
                                    ]),
                            ])
                            ->visible(fn(?FieldKitDefinition $record) =>
                                $record && in_array($record->type, ['select', 'radio'])
                            ),

                        Forms\Components\Tabs\Tab::make('External Mappings')
                            ->schema([
                                Forms\Components\Section::make('System Integrations')
                                    ->schema([
                                        Forms\Components\Repeater::make('external_mappings')
                                            ->label('External Mappings')
                                            ->schema([
                                                Forms\Components\Select::make('adapter_type')
                                                    ->label('Adapter Type')
                                                    ->options([
                                                        'ameax_column' => 'Ameax Database Column',
                                                        'mailchimp_api' => 'Mailchimp API',
                                                        'custom_webhook' => 'Custom Webhook',
                                                    ])
                                                    ->required(),

                                                Forms\Components\TextInput::make('target')
                                                    ->label('Target')
                                                    ->required()
                                                    ->placeholder('customer.phone_number')
                                                    ->helperText('Dot notation path (e.g., customer.phone_number)'),

                                                Forms\Components\KeyValue::make('config')
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

                        Forms\Components\Tabs\Tab::make('Conditional Visibility')
                            ->schema([
                                Forms\Components\Section::make('Display Conditions')
                                    ->schema([
                                        Forms\Components\Repeater::make('conditions')
                                            ->label('Visibility Conditions')
                                            ->schema([
                                                Forms\Components\Select::make('field_type')
                                                    ->label('Field Type')
                                                    ->options([
                                                        'native' => 'Native Field',
                                                        'fieldkit' => 'FieldKit Field',
                                                    ])
                                                    ->required()
                                                    ->live(),

                                                Forms\Components\TextInput::make('field_key')
                                                    ->label('Field Key')
                                                    ->required()
                                                    ->helperText('Key of the field this depends on'),

                                                Forms\Components\Select::make('operator')
                                                    ->label('Operator')
                                                    ->options([
                                                        'in' => 'Value is in list',
                                                        'not_in' => 'Value is not in list',
                                                        'equals' => 'Value equals',
                                                        'not_equals' => 'Value does not equal',
                                                    ])
                                                    ->required(),

                                                Forms\Components\TagsInput::make('expected_values')
                                                    ->label('Expected Values')
                                                    ->helperText('Values that will show/hide this field')
                                                    ->separator(','),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Condition')
                                            ->collapsible(),
                                    ]),

                                Forms\Components\Section::make('Condition Logic')
                                    ->schema([
                                        Forms\Components\Select::make('condition_logic')
                                            ->label('Condition Logic')
                                            ->options([
                                                'AND' => 'All conditions must be met (AND)',
                                                'OR' => 'Any condition can be met (OR)',
                                            ])
                                            ->default('AND')
                                            ->helperText('How multiple conditions are evaluated'),
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
                Tables\Columns\TextColumn::make('form.name')
                    ->label('Form')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('field_key')
                    ->label('Field Key')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('options_count')
                    ->label('Options')
                    ->counts('options')
                    ->sortable()
                    ->alignCenter()
                    ->visible(fn($record) => in_array($record?->type, ['select', 'radio'])),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fieldkit_form_id')
                    ->label('Form')
                    ->relationship('form', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(fn() => app(FieldKitInputRegistry::class)->getOptionsForAdmin()),

                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Required'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            \Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\RelationManagers\OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages\ListFieldKitDefinitions::class,
            'create' => \Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages\CreateFieldKitDefinition::class,
            'edit' => \Ameax\FieldkitFilament\Resources\FieldKitDefinitionResource\Pages\EditFieldKitDefinition::class,
        ];
    }
}