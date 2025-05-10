<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Breeds;
use App\Models\Animals;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AnimalsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AnimalsResource\RelationManagers;

class AnimalsResource extends Resource
{
    protected static ?string $model = Animals::class;

    protected static ?string $navigationIcon = 'lucide-paw-print';

    protected static ?string $navigationLabel = 'Animals';

    protected static ?string $modelLabel = 'Animal';

    protected static ?string $pluralModelLabel = 'Animals';

    protected static ?int $navigationSort = 13;

    protected static ?string $navigationGroup = 'Animals Resource';

    protected static ?string $navigationBadgeTooltip = 'Active Animals for sale';

    public static function getNavigationBadge(): ?string
    {
        return (string) Animals::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Primary details about the animal')
                    ->icon('heroicon-o-identification')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Animal Name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->placeholder('e.g., Max, Bella, Charlie')
                                    ->columnSpan(['md' => 2])
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        if (!empty($state)) {
                                            $set('slug', Animals::generateUniqueSlug($state));
                                        }
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255)
                                    ->columnSpan(['md' => 2])
                                    ->helperText('Automatically generated from animal name'),

                                Forms\Components\TextInput::make('barcode')
                                    ->label('Barcode/ID')
                                    ->maxLength(255)
                                    ->placeholder('e.g., ANM-123456')
                                    ->prefixIcon('lucide-barcode')
                                    ->columnSpan(['md' => 2]),
                            ])
                            ->columns(2),

                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->label('Animal Category')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->prefixIcon('heroicon-o-circle-stack')
                            ->placeholder('Select animal category')
                            ->helperText('Choose or create new category')
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
                            ]),

                        Forms\Components\Select::make('breeds_id')
                            ->relationship('breeds', 'name')
                            ->label('Animal Breed')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->prefixIcon('lucide-dna')
                            ->placeholder('Select animal breed')
                            ->helperText('Choose or create new breed')
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

                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Main Photo')
                            ->image()
                            ->required()
                            ->directory('animal-thumbnails')
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->panelLayout('integrated')
                            ->deleteUploadedFileUsing(function ($state, $livewire, $record) {
                                if ($record?->thumbnail) {
                                    Storage::disk('public')->delete($record->thumbnail);
                                }
                                return true;
                            })
                            ->columnSpanFull()
                            ->helperText('Upload a high-quality square image (800×800px recommended)')
                            ->hint('Click to upload or drag & drop')
                            ->hintIcon('heroicon-o-information-circle'),

                        Forms\Components\Repeater::make('photos')
                            ->label('Additional Photos')
                            ->relationship('animalsPhotos')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Image')
                                    ->image()
                                    ->directory('animal-gallery')
                                    ->imageEditor()
                                    ->downloadable()
                                    ->openable()
                                    ->panelLayout('integrated')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->deleteUploadedFileUsing(function ($state, $livewire, $record) {
                                        if ($record?->photo) {
                                            Storage::disk('public')->delete($record->photo);
                                        }
                                        return true;
                                    })
                                    ->required()
                                    ->helperText('Additional animal photos from different angles'),
                            ])
                            ->grid(2)
                            ->defaultItems(1)
                            ->createItemButtonLabel('+ Add Photo')
                            ->collapsible()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Animal Description')
                            ->columnSpanFull()
                            ->rows(4)
                            ->placeholder('Describe the animal characteristics, personality, and special features...')
                            ->helperText('This will be displayed on the animal profile page'),
                    ]),

                Forms\Components\Section::make('Physical Attributes')
                    ->description('Details about the animal physical characteristics')
                    ->icon('heroicon-o-scale')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('age')
                                    ->label('Age')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('months')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\TextInput::make('weight')
                                    ->label('Weight')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('kg')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\Select::make('gender')
                                    ->label('Gender')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                        'unknown' => 'Unknown'
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(3)
                            ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-800 p-4 rounded-lg']),
                    ]),

                Forms\Components\Section::make('Health Information')
                    ->description('Medical and health details')
                    ->icon('heroicon-o-heart')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('health_status')
                                    ->label('Health Status')
                                    ->options([
                                        'excellent' => 'Excellent',
                                        'good' => 'Good',
                                        'fair' => 'Fair',
                                        'poor' => 'Poor',
                                        'critical' => 'Critical'
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\TextInput::make('stock')
                                    ->label('Available Quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefixIcon('heroicon-o-archive-box')
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\Toggle::make('vaccination_status')
                                    ->label('Vaccinated')
                                    ->required()
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(3),
                    ]),

                Forms\Components\Section::make('Pricing')
                    ->description('Set pricing for the animal')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('purchase_price')
                                    ->label('Purchase Price')
                                    ->required()
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

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Available for Sale')
                                    ->required()
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('')
                    ->square()
                    ->width(60)
                    ->height(60)
                    ->grow(false)
                    ->toggleable()
                    ->extraImgAttributes(['class' => 'rounded-lg border border-gray-200']),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Animal Name')
                    ->weight(FontWeight::Bold)
                    ->description(fn($record) => $record->breeds->name ?? '')
                    ->wrap()
                    ->tooltip(fn($record) => $record->description)
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('categoryAnimals.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-circle-stack')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('age')
                    ->label('Age')
                    ->sortable()
                    ->formatStateUsing(fn($state): string => "{$state} months")
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'warning',
                        default => 'gray',
                    })
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('health_status')
                    ->label('Health')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'excellent' => 'success',
                        'good' => 'primary',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    })
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Price')
                    ->sortable()
                    ->money('IDR', locale: 'id')
                    ->color('success')
                    ->weight(FontWeight::Bold)
                    ->description(fn($record) => 'Margin: Rp' . number_format($record->selling_price - $record->purchase_price, 0, ',', '.'))
                    ->alignEnd(),

                // Tables\Columns\TextColumn::make('stock')
                //     ->label('Stock')
                //     ->numeric()
                //     ->sortable()
                //     ->color(fn($state) => $state <= 1 ? 'danger' : ($state <= 3 ? 'warning' : 'success'))
                //     ->weight(FontWeight::Bold)
                //     ->icon(fn($state) => match (true) {
                //         $state <= 1 => 'heroicon-o-exclamation-triangle',
                //         $state <= 3 => 'heroicon-o-arrow-trending-down',
                //         default => 'heroicon-o-check-badge'
                //     })
                //     ->tooltip(fn($state) => match (true) {
                //         $state <= 1 => 'Critical stock! Only 1 left',
                //         $state <= 3 => 'Low stock - consider reordering',
                //         default => 'Stock available'
                //     })
                //     ->alignCenter(),

                Tables\Columns\IconColumn::make('vaccination_status')
                    ->label('Vaccinated')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->alignCenter()
                    ->toggleable(),

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
                    ->label('Breed')
                    ->relationship('breeds', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'unknown' => 'Unknown'
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('health_status')
                    ->options([
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                        'critical' => 'Critical'
                    ])
                    ->native(false),

                Tables\Filters\TernaryFilter::make('vaccination_status')
                    ->label('Vaccination Status')
                    ->trueLabel('Vaccinated animals')
                    ->falseLabel('Non-vaccinated animals')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Availability Status')
                    ->trueLabel('Available animals')
                    ->falseLabel('Unavailable animals')
                    ->native(false),

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
                    ->tooltip('Edit Animal'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Delete Animal'),

                Tables\Actions\RestoreAction::make()
                    ->iconButton()
                    ->color('warning')
                    ->tooltip('Restore Animal'),

                Tables\Actions\ForceDeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Force Delete Animal')
                    ->before(function (Animals $record) {
                        // Delete thumbnail
                        if ($record->thumbnail) {
                            Storage::disk('public')->delete($record->thumbnail);
                        }

                        // Delete all animal photos
                        $record->animalsPhotos->each(function ($photo) {
                            if ($photo->photo) {
                                Storage::disk('public')->delete($photo->photo);
                            }
                        });
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
                                if ($record->thumbnail) {
                                    Storage::disk('public')->delete($record->thumbnail);
                                }
                                $record->animalsPhotos->each(function ($photo) {
                                    if ($photo->photo) {
                                        Storage::disk('public')->delete($photo->photo);
                                    }
                                });
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No animals found')
            ->emptyStateDescription('Click "Create animal" to add your first animal')
            ->emptyStateIcon('heroicon-o-paw')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create animal')
                    ->icon('heroicon-o-plus'),
            ])
            ->striped()
            ->deferLoading()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->groups([
                Tables\Grouping\Group::make('categoryAnimals.name')
                    ->label('Category')
                    ->collapsible(),

                Tables\Grouping\Group::make('breeds.name')
                    ->label('Breed')
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
            'index' => Pages\ListAnimals::route('/'),
            'create' => Pages\CreateAnimals::route('/create'),
            // 'edit' => Pages\EditAnimals::route('/{record}/edit'),
        ];
    }
}
