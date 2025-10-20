<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources;

use Ameax\FieldkitCore\Models\FieldKitForm;
use App\Filament\Resources\Resource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FieldKitFormResource extends Resource
{
    protected static ?string $model = FieldKitForm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public static function getNavigationSort(): ?int
    {
        return 900;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('purpose_token')
                            ->label('Purpose Token')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique identifier for this form (e.g., customer_registration)')
                            ->placeholder('customer_registration'),
                        
                        TextInput::make('name')
                            ->label('Form Name')
                            ->required()
                            ->placeholder('Customer Registration'),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->placeholder('Additional fields for customer registration'),
                        
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Multi-Tenancy (Optional)')
                    ->schema([
                        TextInput::make('owner_type')
                            ->label('Owner Type')
                            ->placeholder('App\\Models\\Shop'),
                        
                        TextInput::make('owner_id')
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
                TextColumn::make('purpose_token')
                    ->label('Purpose Token')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('name')
                    ->label('Form Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('fields_count')
                    ->label('Fields')
                    ->counts('fields')
                    ->sortable(),
                
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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