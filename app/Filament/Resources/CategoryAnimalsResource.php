<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryAnimalsResource\Pages;
use App\Filament\Resources\CategoryAnimalsResource\RelationManagers;

class CategoryAnimalsResource extends Resource
{
    protected static ?string $model = CategoryAnimals::class;

    protected static ?string $navigationLabel = 'Animal Category';
    protected static ?string $modelLabel = 'Animal Category';
    protected static ?string $pluralModelLabel = 'Animal Category';
    protected static ?string $navigationGroup = 'Animals Resource';
    protected static ?int $navigationSort = 11;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Information')
                    ->description('Provide basic details about the animal category')
                    ->icon('heroicon-o-tag')
                    ->collapsible()
                    ->collapsed(false) // Open by default for better UX
                    ->schema([
                        // Name and Slug in a compact grid
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

                // Additional section for SEO or advanced options could be added here
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
                    ->weight('medium'),
                // ->description(fn(CategoryAnimals $record): string => $record->description ? Str::limit($record->description, 50) : ''),

                Tables\Columns\TextColumn::make('animals_count')
                    ->label('Animals')
                    ->counts('animals')
                    ->sortable()
                    ->color('success')
                    ->icon('lucide-paw-print'),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->sortable()
                    ->color('info')
                    ->icon('heroicon-o-shopping-bag'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('has_animals')
                    ->label('Has Animals')
                    ->options([
                        'yes' => 'With Animals',
                        'no' => 'Without Animals',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'yes') {
                            $query->has('animals');
                        } elseif ($data['value'] === 'no') {
                            $query->doesntHave('animals');
                        }
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
                    ->before(function (CategoryAnimals $record) {
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
            ->emptyStateDescription('No animal categories found. Create one to get started!')
            ->defaultSort('name', 'asc')
            ->deferLoading()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession();
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
            'index' => Pages\ListCategoryAnimals::route('/'),
            'create' => Pages\CreateCategoryAnimals::route('/create'),
        ];
    }
}
