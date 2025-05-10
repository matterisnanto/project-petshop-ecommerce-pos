<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Hotel;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\HotelResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HotelResource\RelationManagers;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon = 'lucide-hotel';

    protected static ?string $navigationLabel = 'Hotel';

    protected static ?string $modelLabel = 'Hotel Service';

    protected static ?string $pluralModelLabel = 'Hotel Services';

    protected static ?string $navigationGroup = 'Service Resource';

    protected static ?int $navigationSort = 17;

    protected static ?string $navigationBadgeTooltip = 'Active Hotel Services';

    public static function getNavigationBadge(): ?string
    {
        return (string) Hotel::where('is_active', true)->count();
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
                    ->description('Enter basic details about your hotel service')
                    ->icon('heroicon-o-building-office')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Service Name')
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(255)
                            ->placeholder('e.g., Premium Pet Hotel')
                            ->columnSpan(['md' => 2])
                            ->afterStateUpdated(function (Set $set, $state) {
                                if (!empty($state)) {
                                    $set('slug', Hotel::generateUniqueSlug($state));
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
                            ->placeholder('Describe the service features, benefits, and what it includes...')
                            ->helperText('This will be displayed on the service page'),
                    ]),

                Forms\Components\Section::make('Service Packages')
                    ->description('Available hotel service packages')
                    ->icon('heroicon-o-rectangle-stack')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Repeater::make('hotelPackage')
                            ->relationship('hotelPackage')
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

                Forms\Components\Section::make('Categories')
                    ->description('Service classification')
                    ->icon('heroicon-o-tag')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->label('Animal Category')
                            ->searchable()
                            ->preload()
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
                            ->columnSpanFull()
                            ->helperText('Which animals is this service for?'),
                    ]),

                Forms\Components\Section::make('Pricing & Availability')
                    ->description('Set service pricing and availability')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('price_per_day')
                                    ->label('Daily Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->inputMode('decimal')
                                    ->step(1000)
                                    ->minValue(0)
                                    ->columnSpan(['md' => 1]),

                                Forms\Components\TextInput::make('capacity')
                                    ->label('Room Capacity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->step(1)
                                    ->prefixIcon('heroicon-o-home-modern')
                                    ->columnSpan(['md' => 1]),
                            ])
                            ->columns(2)
                            ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-800 p-4 rounded-lg']),
                    ]),

                Forms\Components\Section::make('Service Media')
                    ->description('Upload service images')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Main Service Image')
                            ->image()
                            ->directory('hotel-service-photos')
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->panelLayout('integrated')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('4:3')
                            ->deleteUploadedFileUsing(function ($state, $livewire, $record) {
                                if ($record?->thumbnail) {
                                    Storage::disk('public')->delete($record->thumbnail);
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

                Tables\Columns\TextColumn::make('price_per_day')
                    ->label('Price')
                    ->formatStateUsing(fn($state) => 'Rp' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->color('success')
                    ->weight(FontWeight::Bold)
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacity')
                    ->numeric()
                    ->sortable()
                    ->color(fn($state) => $state <= 2 ? 'danger' : ($state <= 5 ? 'warning' : 'success'))
                    ->weight(FontWeight::Bold)
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

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active services')
                    ->falseLabel('Inactive services')
                    ->native(false),

                Tables\Filters\Filter::make('low_capacity')
                    ->label('Low Capacity Alert')
                    ->query(fn(Builder $query) => $query->where('capacity', '<=', 2))
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
                    ->before(function (Hotel $record) {
                        if ($record->thumbnail) {
                            Storage::disk('public')->delete($record->thumbnail);
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
                                if ($record->thumbnail) {
                                    Storage::disk('public')->delete($record->thumbnail);
                                }
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No hotel services found')
            ->emptyStateDescription('Click "Create service" to add your first hotel service')
            ->emptyStateIcon('heroicon-o-building-office')
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
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            // 'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
