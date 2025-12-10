<?php

declare(strict_types=1);

namespace Ameax\FieldkitFilament\Resources;

use Ameax\FieldkitCore\Contracts\ContextProviderInterface;
use Ameax\FieldkitCore\Models\FieldKitForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
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
                        static::getPurposeTokenField(),

                        TextInput::make('name')
                            ->label(__('fieldkit-filament::resources.forms.fields.name.label'))
                            ->required()
                            ->placeholder(__('fieldkit-filament::resources.forms.fields.name.placeholder'))
                            ->afterStateUpdated(function (string $operation, $state, callable $set) {
                                // Only auto-fill purpose_token if using TextInput (no predefined tokens)
                                if ($operation !== 'create' || ! empty(config('fieldkit.purpose_tokens', []))) {
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

                        TextInput::make('priority')
                            ->label(__('fieldkit-filament::resources.forms.fields.priority.label'))
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(255)
                            ->helperText(__('fieldkit-filament::resources.forms.fields.priority.helper')),
                    ])
                    ->columns(2),

                ...static::getContextSection(),
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

                TextColumn::make('priority')
                    ->label(__('fieldkit-filament::resources.forms.fields.priority.label'))
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 3 => 'success',
                        $state <= 7 => 'warning',
                        default => 'gray',
                    }),

                ...static::getContextTableColumns(),

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
            ->defaultSort('priority', 'asc');
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

    /**
     * Get the purpose token field - Select if tokens configured, TextInput otherwise
     */
    protected static function getPurposeTokenField(): Select|TextInput
    {
        $purposeTokens = config('fieldkit.purpose_tokens', []);

        // If purpose tokens are configured, use Select dropdown
        if (! empty($purposeTokens)) {
            return Select::make('purpose_token')
                ->label(__('fieldkit-filament::resources.forms.fields.purpose_token.label'))
                ->required()
                ->options($purposeTokens)
                ->helperText(__('fieldkit-filament::resources.forms.fields.purpose_token.helper'))
                ->searchable();
        }

        // Otherwise, use TextInput (original behavior)
        return TextInput::make('purpose_token')
            ->label(__('fieldkit-filament::resources.forms.fields.purpose_token.label'))
            ->required()
            ->helperText(__('fieldkit-filament::resources.forms.fields.purpose_token.helper'))
            ->placeholder(__('fieldkit-filament::resources.forms.fields.purpose_token.placeholder'));
    }

    /**
     * Get context section for the form
     *
     * @return array<Section>
     */
    protected static function getContextSection(): array
    {
        $provider = static::getContextProvider();

        if (! $provider) {
            // No context provider configured - show legacy multi-tenancy fields
            return [
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
            ];
        }

        $fields = $provider->getFormFields();
        if (empty($fields)) {
            return [];
        }

        return [
            Section::make($provider->getSectionLabel())
                ->description($provider->getSectionDescription())
                ->schema($fields)
                ->columns(2)
                ->collapsible(),
        ];
    }

    /**
     * Get context columns for the table
     *
     * @return array<mixed>
     */
    protected static function getContextTableColumns(): array
    {
        $provider = static::getContextProvider();

        if (! $provider) {
            return [];
        }

        return $provider->getTableColumns();
    }

    protected static function getContextProvider(): ?ContextProviderInterface
    {
        if (! config('fieldkit.context.enabled', false)) {
            return null;
        }

        // Use form_provider for form-level context, fallback to provider for backwards compatibility
        $providerConfig = config('fieldkit.context.form_provider') ?? config('fieldkit.context.provider');

        if (! $providerConfig) {
            return null;
        }

        // Handle closure (factory method) or class string
        if ($providerConfig instanceof \Closure) {
            return $providerConfig();
        }

        if (is_string($providerConfig) && class_exists($providerConfig)) {
            return app($providerConfig);
        }

        return null;
    }
}
