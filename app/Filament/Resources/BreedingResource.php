<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Breeds;
use Filament\Forms\Set;
use App\Models\Breeding;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BreedingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BreedingResource\RelationManagers;

class BreedingResource extends Resource
{
    protected static ?string $model = Breeding::class;

    protected static ?string $navigationIcon = 'lucide-baby';

    protected static ?string $navigationLabel = 'Breeding';

    protected static ?string $modelLabel = 'Breeding Service';

    protected static ?string $pluralModelLabel = 'Breeding Services';

    protected static ?string $navigationGroup = 'Service Resource';

    protected static ?int $navigationSort = 16;

    protected static ?string $navigationBadgeTooltip = 'Active Breeding Services';

    public static function getNavigationBadge(): ?string
    {
        return (string) Breeding::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Service Information')
                    ->description('Enter basic details about your breeding service')
                    ->icon('heroicon-o-sparkles')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Service Name')
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(255)
                            ->placeholder('e.g., Premium Dog Breeding')
                            ->columnSpan(['md' => 2])
                            ->afterStateUpdated(function (Set $set, $state) {
                                if (!empty($state)) {
                                    $set('slug', Breeding::generateUniqueSlug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->readOnly()
                            ->maxLength(255)
                            ->columnSpan(['md' => 2])
                            ->helperText('Automatically generated from service name'),

                        Forms\Components\Textarea::make('description')
                            ->label('Service Description')
                            ->columnSpanFull()
                            ->rows(4)
                            ->placeholder('Describe the breeding service features and what it includes...')
                            ->helperText('This will be displayed on the service page'),
                    ]),

                Forms\Components\Section::make('Service Packages')
                    ->description('Available breeding service packages')
                    ->icon('heroicon-o-rectangle-stack')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Repeater::make('breedingPackage')
                            ->relationship('breedingPackage')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->placeholder('Package name')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\Textarea::make('description')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpan(['md' => 2]),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->addActionLabel('Add Package')
                            ->defaultItems(1)
                            ->collapsible(),
                    ]),

                Forms\Components\Section::make('Animal Categories')
                    ->description('Animal classification')
                    ->icon('heroicon-o-tag')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->label('Animal Category')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->placeholder('Select animal category')
                            ->createOptionForm([
                                Forms\Components\Section::make('Category Information')
                                    ->description('Provide basic details about the animal category')
                                    ->icon('heroicon-o-tag')
                                    ->collapsible()
                                    ->collapsed(false)
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Category Name')
                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                        if (!empty($state)) {
                                                            $set('slug', CategoryAnimals::generateUniqueSlug($state));
                                                        }
                                                    })
                                                    ->required()
                                                    ->live(onBlur: true)
                                                    ->maxLength(255)
                                                    ->placeholder('e.g., Dogs, Cats, Birds')
                                                    ->helperText('The display name that will appear throughout the site')
                                                    ->columnSpan(['md' => 2])
                                                    ->prefixIcon('heroicon-o-tag')
                                                    ->autofocus(),

                                                Forms\Components\TextInput::make('slug')
                                                    ->label('URL Slug')
                                                    ->required()
                                                    ->readOnly()
                                                    ->maxLength(255)
                                                    ->helperText('Auto-generated SEO-friendly URL identifier')
                                                    ->columnSpan(['md' => 2])
                                                    ->prefixIcon('heroicon-o-link'),
                                            ])
                                            ->columns(2),

                                        Forms\Components\Textarea::make('description')
                                            ->label('Description')
                                            ->maxLength(255)
                                            ->rows(3)
                                            ->placeholder('Brief description about this animal category')
                                            ->helperText(function (?string $state): string {
                                                $length = strlen($state ?? '');
                                                return "{$length}/255 characters";
                                            })
                                            ->reactive()
                                            ->columnSpanFull(),

                                        Forms\Components\Fieldset::make('Visual Representation')
                                            ->schema([
                                                Forms\Components\FileUpload::make('icon')
                                                    ->label('Category Icon/Image')
                                                    ->image()
                                                    ->directory('category-icons')
                                                    ->imageEditor()
                                                    ->imageResizeMode('cover')
                                                    ->imageCropAspectRatio('1:1')
                                                    ->imagePreviewHeight('200')
                                                    ->imageResizeTargetWidth('300')
                                                    ->imageResizeTargetHeight('300')
                                                    ->deleteUploadedFileUsing(function ($state, $livewire, $record) {
                                                        if ($record?->icon) {
                                                            Storage::disk('public')->delete($record->icon);
                                                        }
                                                        return true;
                                                    })
                                                    ->panelLayout('integrated')
                                                    ->maxSize(1024)
                                                    ->helperText('Recommended size: 512x512px transparent PNG')
                                                    ->downloadable()
                                                    ->openable()
                                                    ->columnSpanFull(),
                                            ])
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(['md' => 1])
                            ->helperText('Which animals is this breeding service for?'),

                        Forms\Components\Select::make('breeds_id')
                            ->relationship('breeds', 'name')
                            ->label('Animal Breed')
                            ->placeholder('Select animal breed')
                            ->native(false)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(['md' => 1])
                            ->helperText('Specific breed for this breeding service')
                            ->createOptionForm([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Section::make('Breed Details')
                                            ->description('Add or modify animal breed information')
                                            ->icon('heroicon-o-information-circle')
                                            ->collapsible()
                                            ->compact()
                                            ->columns(['md' => 2])
                                            ->schema([
                                                // Animal Category Select with visual enhancements
                                                Forms\Components\Select::make('category_animals_id')
                                                    ->relationship('categoryAnimals', 'name')
                                                    ->label('Animal Category')
                                                    ->searchable()
                                                    ->preload()
                                                    ->native(false)
                                                    ->placeholder('Select animal category')
                                                    ->required()
                                                    ->columnSpanFull()
                                                    ->createOptionForm([
                                                        Forms\Components\Section::make('Category Information')
                                                            ->description('Provide basic details about the animal category')
                                                            ->icon('heroicon-o-tag')
                                                            ->collapsible()
                                                            ->collapsed(false)
                                                            ->schema([
                                                                Forms\Components\Grid::make()
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('name')
                                                                            ->label('Category Name')
                                                                            ->afterStateUpdated(function (Set $set, $state) {
                                                                                if (!empty($state)) {
                                                                                    $set('slug', CategoryAnimals::generateUniqueSlug($state));
                                                                                }
                                                                            })
                                                                            ->required()
                                                                            ->live(onBlur: true)
                                                                            ->maxLength(255)
                                                                            ->placeholder('e.g., Dogs, Cats, Birds')
                                                                            ->helperText('The display name that will appear throughout the site')
                                                                            ->columnSpan(['md' => 2])
                                                                            ->prefixIcon('heroicon-o-tag')
                                                                            ->autofocus(), // Auto focus for better UX

                                                                        Forms\Components\TextInput::make('slug')
                                                                            ->label('URL Slug')
                                                                            ->required()
                                                                            ->readOnly()
                                                                            ->maxLength(255)
                                                                            ->helperText('Auto-generated SEO-friendly URL identifier')
                                                                            ->columnSpan(['md' => 2])
                                                                            ->prefixIcon('heroicon-o-link'),
                                                                    ])
                                                                    ->columns(2),

                                                                // Description with character counter
                                                                Forms\Components\Textarea::make('description')
                                                                    ->label('Description')
                                                                    ->maxLength(255)
                                                                    ->rows(3)
                                                                    ->placeholder('Brief description about this animal category (e.g., "Includes all breeds of domestic dogs")')
                                                                    ->helperText(function (?string $state): string {
                                                                        $length = strlen($state ?? '');
                                                                        return "{$length}/255 characters";
                                                                    })
                                                                    ->reactive()
                                                                    ->columnSpanFull(),

                                                                Forms\Components\Fieldset::make('Visual Representation')
                                                                    ->schema([
                                                                        Forms\Components\FileUpload::make('icon')
                                                                            ->label('Category Icon/Image')
                                                                            ->image()
                                                                            ->directory('category-icons')
                                                                            ->imageEditor()
                                                                            ->imageResizeMode('cover')
                                                                            ->imageCropAspectRatio('1:1')
                                                                            ->imagePreviewHeight('200')
                                                                            ->imageResizeTargetWidth('300')
                                                                            ->imageResizeTargetHeight('300')
                                                                            ->deleteUploadedFileUsing(function ($state, $livewire, $record) {

                                                                                if ($record?->icon) {
                                                                                    Storage::disk('public')->delete($record->icon);
                                                                                }
                                                                                return true;
                                                                            })
                                                                            ->panelLayout('integrated')
                                                                            ->maxSize(1024)
                                                                            ->helperText('Recommended size: 512x512px transparent PNG')
                                                                            ->downloadable()
                                                                            ->openable()
                                                                            ->columnSpanFull(),
                                                                    ])
                                                            ])
                                                            ->columns(2),
                                                    ]),

                                                Forms\Components\TextInput::make('name')
                                                    ->label('Breed Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                        $set('slug', Breeds::generateUniqueSlug($state));
                                                    })
                                                    ->columnSpan(['md' => 2]),

                                                Forms\Components\TextInput::make('slug')
                                                    ->label('URL Slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->columnSpan(['md' => 2]),
                                            ]),
                                    ]),
                            ]),
                    ]),

                Forms\Components\Section::make('Pricing & Availability')
                    ->description('Set service pricing and availability')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('stock')
                                    ->label('Available Stock')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefixIcon('heroicon-o-archive-box')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\TextInput::make('purchase_price')
                                    ->label('Cost Price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rp')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\TextInput::make('selling_price')
                                    ->label('Selling Price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rp')
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(3)
                            ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-800 p-4 rounded-lg']),
                    ]),

                Forms\Components\Section::make('Service Media')
                    ->description('Upload service images')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label('Main Service Image')
                            ->image()
                            ->directory('breeding-service-photos')
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->panelLayout('integrated')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('4:3')
                            ->deleteUploadedFileUsing(function ($state, $livewire, $record) {
                                if ($record?->photo) {
                                    Storage::disk('public')->delete($record->photo);
                                }
                                return true;
                            })
                            ->columnSpanFull()
                            ->helperText('Upload a high-quality image (1200×900px recommended)')
                            ->hint('Click to upload or drag & drop')
                            ->hintIcon('heroicon-o-information-circle'),
                    ]),

                Forms\Components\Section::make('Status')
                    ->description('Control service visibility')
                    ->icon('heroicon-o-eye')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Service')
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false)
                            ->helperText('Visible to customers when active')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->square()
                    ->width(60)
                    ->height(60)
                    ->grow(false)
                    ->toggleable()
                    ->extraImgAttributes(['class' => 'rounded-lg border border-gray-200']),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Service Name')
                    ->weight(FontWeight::Bold)
                    ->wrap()
                    ->tooltip(fn($record) => $record->description)
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('categoryAnimals.name')
                    ->label('Animal')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->icon('lucide-paw-print')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('breeds.name')
                    ->label('Breed')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-sparkles')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Price')
                    ->formatStateUsing(fn($state) => 'Rp' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->color('success')
                    ->weight(FontWeight::Bold)
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn($state) => $state <= 2 ? 'danger' : ($state <= 5 ? 'warning' : 'success'))
                    ->weight(FontWeight::Bold)
                    // ->icon(fn($state) => match (true) {
                    //     $state <= 2 => 'heroicon-o-exclamation-triangle',
                    //     $state <= 5 => 'heroicon-o-arrow-trending-down',
                    //     default => 'heroicon-o-check-badge'
                    // })
                    // ->tooltip(fn($state) => match (true) {
                    //     $state <= 2 => 'Critical stock! Limited availability',
                    //     $state <= 5 => 'Low stock - consider restocking',
                    //     default => 'Stock available'
                    // })
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-o-x-circle')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_animals_id')
                    ->label('Animal Category')
                    ->relationship('categoryAnimals', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('breeds_id')
                    ->label('Animal Breed')
                    ->relationship('breeds', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active services')
                    ->falseLabel('Inactive services')
                    ->native(false),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock Alert')
                    ->query(fn(Builder $query) => $query->where('stock', '<=', 2))
                    ->toggle(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->color('primary')
                    ->tooltip('View Details'),

                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->color('success')
                    ->tooltip('Edit Service'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Delete Service'),

                Tables\Actions\RestoreAction::make()
                    ->iconButton()
                    ->color('warning')
                    ->tooltip('Restore Service'),

                Tables\Actions\ForceDeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Force Delete Service')
                    ->before(function (Breeding $record) {
                        if ($record->photo) {
                            Storage::disk('public')->delete($record->photo);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation(),

                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restore Selected')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->requiresConfirmation(),

                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Permanently Delete Selected')
                        ->icon('heroicon-o-trash')
                        ->before(function ($records) {
                            $records->each(function ($record) {
                                if ($record->photo) {
                                    Storage::disk('public')->delete($record->photo);
                                }
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No breeding services found')
            ->emptyStateDescription('Click "Create service" to add your first breeding service')
            ->emptyStateIcon('heroicon-o-sparkles')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create service')
                    ->icon('heroicon-o-plus'),
            ])
            ->striped()
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->groups([
                Tables\Grouping\Group::make('categoryAnimals.name')
                    ->label('Animal Category')
                    ->collapsible(),

                Tables\Grouping\Group::make('breeds.name')
                    ->label('Animal Breed')
                    ->collapsible(),
            ])
            ->groupingSettingsInDropdownOnDesktop()
            ->defaultGroup('categoryAnimals.name');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBreedings::route('/'),
            'create' => Pages\CreateBreeding::route('/create'),
            // 'edit' => Pages\EditBreeding::route('/{record}/edit'),
        ];
    }
}
