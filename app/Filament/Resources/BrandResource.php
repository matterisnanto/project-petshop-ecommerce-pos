<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BrandResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BrandResource\RelationManagers;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Brands';

    protected static ?string $modelLabel = 'Brand';

    protected static ?string $pluralModelLabel = 'Brands';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationGroup = 'Product Resource';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Brand Information')
                    ->description('Enter your brand details')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Brand Name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        if (!empty($state)) {
                                            $set('slug', Brand::generateUniqueSlug($state));
                                        }
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->placeholder('e.g., Whiskas, Purina')
                                    ->helperText('The official name of your brand')
                                    ->columnSpan(['md' => 2]),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255)
                                    ->helperText('Auto-generated from name')
                                    ->columnSpan(['md' => 2]),
                            ])
                            ->columns(2),

                        Forms\Components\FileUpload::make('logo')
                            ->label('Brand Logo')
                            ->image()
                            ->directory('brand-logos')
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('300')
                            ->deleteUploadedFileUsing(function ($state, $livewire, $record) {

                                if ($record?->logo) {
                                    Storage::disk('public')->delete($record->logo);
                                }
                                return true;
                            })
                            ->imagePreviewHeight('150')
                            ->panelAspectRatio('1:1')
                            ->panelLayout('integrated')
                            ->maxSize(1024)
                            ->required()
                            ->downloadable()
                            ->openable()
                            ->helperText('Upload a square logo (max 1MB)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->size(60)
                    ->extraImgAttributes(['class' => 'rounded-lg']),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn(Brand $record) => $record->slug)
                    ->wrap(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state < 5 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('products_count')
                    ->label('Products Count')
                    ->options([
                        '0' => 'No products',
                        '1-5' => '1-5 products',
                        '5+' => '5+ products',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value']) {
                            '0' => $query->has('products', '=', 0),
                            '1-5' => $query->has('products', '<=', 5)->has('products', '>', 0),
                            '5+' => $query->has('products', '>', 5),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->color('primary')
                    ->tooltip('View Details'),

                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->color('success')
                    ->tooltip('Edit Brand'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Delete Brand'),

                Tables\Actions\RestoreAction::make()
                    ->iconButton()
                    ->color('warning')
                    ->tooltip('Restore Brand'),
                Tables\Actions\ForceDeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Force Delete Brand')
                    ->before(function (Brand $record) {
                        // Hapus file sebelum record dihapus
                        if ($record->logo) {
                            Storage::disk('public')->delete($record->logo);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->icon('heroicon-o-trash')
                        ->modalHeading('Delete selected brands')
                        ->modalDescription('Are you sure you want to delete these brands? This action cannot be undone.'),

                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restore Selected')
                        ->icon('heroicon-o-arrow-uturn-left'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Permanently Delete Selected')
                        ->icon('heroicon-o-trash')
                        ->before(function (Collection $records) {
                            $records->each(function ($record) {
                                if ($record->logo) {
                                    Storage::disk('public')->delete($record->logo);
                                }
                            });
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add New Brand')
                    ->icon('heroicon-o-plus'),
            ])
            ->emptyStateDescription('No brands found. Click the button below to add one.')
            ->emptyStateIcon('heroicon-o-sparkles')
            ->deferLoading()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession();
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            // 'view' => Pages\ViewBrand::route('/{record}'),
            // 'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->withCount('products');
    // }
}
