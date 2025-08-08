<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Categories;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoriesResource\Pages;
use App\Filament\Resources\CategoriesResource\RelationManagers;

class CategoriesResource extends Resource
{
    protected static ?string $model = Categories::class;

    protected static ?string $navigationLabel = 'Category';

    protected static ?string $modelLabel = 'Category';

    protected static ?string $pluralModelLabel = 'Category';

    protected static ?string $navigationGroup = 'Product Resource';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Information')
                    ->description('Create or update category details')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Category Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Cat Food, Dog Supplies')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        if (!empty($state)) {
                                            $set('slug', Categories::generateUniqueSlug($state));
                                        }
                                    })
                                    ->columnSpan(['md' => 2]),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-o-link', tooltip: 'Used in URLs')
                                    ->columnSpan(['md' => 2]),
                            ])
                            ->columns(2),

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
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->label('')
                    ->size(40)
                    ->circular()
                    ->defaultImageUrl(url('/img/default-category-icon.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn(Categories $record) => $record->slug),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state < 5 => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->alignEnd(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
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
                    ->tooltip('Edit Category'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Delete Category'),

                Tables\Actions\RestoreAction::make()
                    ->iconButton()
                    ->color('warning')
                    ->tooltip('Restore Category'),
                Tables\Actions\ForceDeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Force Delete Category')
                    ->before(function (Categories $record) {
                        if ($record->icon) {
                            Storage::disk('public')->delete($record->icon);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete selected')
                        ->icon('heroicon-o-trash')
                        ->modalHeading('Delete selected categories')
                        ->modalDescription('Are you sure you want to delete these categories? This action cannot be undone.'),

                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restore Selected')
                        ->icon('heroicon-o-arrow-uturn-left'),

                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Permanently Delete Selected')
                        ->icon('heroicon-o-trash')
                        ->before(function (Collection $records) {
                            $records->each(function ($record) {
                                if ($record->icon) {
                                    Storage::disk('public')->delete($record->icon);
                                }
                            });
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Category')
                    ->icon('heroicon-o-plus'),
            ])
            ->emptyStateDescription('No categories found. Create your first one!')
            ->emptyStateIcon('heroicon-o-tag')
            ->deferLoading()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('products')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategories::route('/create'),
        ];
    }
}
