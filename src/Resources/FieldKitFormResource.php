<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources;

use Ameax\FieldkitCore\Models\FieldKitForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FieldKitFormResource extends Resource
{
    protected static ?string $model = FieldKitForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'FieldKit Forms';

    protected static ?string $navigationGroup = 'FieldKit';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('purpose_token')
                            ->label('Purpose Token')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique identifier for this form (e.g., customer_registration)')
                            ->placeholder('customer_registration'),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Form Name')
                            ->required()
                            ->placeholder('Customer Registration'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->placeholder('Additional fields for customer registration'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Multi-Tenancy (Optional)')
                    ->schema([
                        Forms\Components\TextInput::make('owner_type')
                            ->label('Owner Type')
                            ->placeholder('App\\Models\\Shop'),
                        
                        Forms\Components\TextInput::make('owner_id')
                            ->label('Owner ID')
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
                Tables\Columns\TextColumn::make('purpose_token')
                    ->label('Purpose Token')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Form Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fields_count')
                    ->label('Fields')
                    ->counts('fields')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages\ListFieldKitForms::class,
            'create' => \Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages\CreateFieldKitForm::class,
            'edit' => \Ameax\FieldkitFilament\Resources\FieldKitFormResource\Pages\EditFieldKitForm::class,
        ];
    }
}