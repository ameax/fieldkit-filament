<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources\FieldKitFormResource\RelationManagers;

use Ameax\FieldkitCore\FieldKitInputRegistry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FieldDefinitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';
    
    protected static ?string $title = 'Field Definitions';

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
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Unique identifier for this field (e.g., customer_phone)')
                                            ->placeholder('customer_phone'),

                                        Select::make('type')
                                            ->label('Field Type')
                                            ->options(fn() => app(FieldKitInputRegistry::class)->getOptionsForAdmin())
                                            ->required()
                                            ->helperText('Select the field type'),

                                        TextInput::make('label')
                                            ->label('Label')
                                            ->required()
                                            ->placeholder('Phone Number'),

                                        TextInput::make('sort_order')
                                            ->label('Sort Order')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Fields are displayed in ascending order'),
                                    ])
                                    ->columns(2),

                                Section::make('Display Settings')
                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(2)
                                            ->placeholder('Enter your phone number'),

                                        TextInput::make('placeholder')
                                            ->label('Placeholder')
                                            ->placeholder('+1 (555) 123-4567'),

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

                                        TextInput::make('validation_rules')
                                            ->label('Laravel Validation Rules')
                                            ->placeholder('max:255|alpha_dash')
                                            ->helperText('Laravel validation syntax (e.g., max:255|alpha_dash)'),

                                    ])
                                    ->columns(2),

                            ]),

                        Tab::make('Options')
                            ->schema([
                                Section::make('Field Options')
                                    ->schema([
                                        Placeholder::make('options_info')
                                            ->label('')
                                            ->content('Options are managed through the Options relation manager below this form.'),
                                    ]),
                            ])
                            ->visible(fn(?object $record) =>
                                $record && in_array($record->type ?? '', ['select', 'radio'])
                            ),

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
                                                    ->required(),

                                                TagsInput::make('expected_values')
                                                    ->label('Expected Values')
                                                    ->helperText('Values that will show/hide this field')
                                                    ->separator(','),
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
                    ->label('Add Field'),
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
