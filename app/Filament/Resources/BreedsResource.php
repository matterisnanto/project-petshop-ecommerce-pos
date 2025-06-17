<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Breeds;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BreedsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BreedsResource\RelationManagers;

class BreedsResource extends Resource
{
    protected static ?string $model = Breeds::class;

    protected static ?string $navigationLabel = 'Breeds';
    protected static ?string $modelLabel = 'Breeds';
    protected static ?string $pluralModelLabel = 'Breeds';
    protected static ?string $navigationGroup = 'Animals Resource';
    protected static ?int $navigationSort = 12;

    public static function getNavigationIcon(): string
    {
        return 'lucide-dna';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Breed Details')
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=FFFFFF&background=4f46e5'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Breed Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn($record) => $record->slug),

                Tables\Columns\TextColumn::make('categoryAnimals.name')
                    ->label('Category')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_animals_id')
                    ->label('Category')
                    ->relationship('categoryAnimals', 'name')
                    ->searchable()
                    ->preload(),

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
                    ->before(function (Breeds $record) {
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
                    ->label('Add New Breed')
                    ->icon('heroicon-o-plus'),
            ])
            ->emptyStateIcon('heroicon-o-sparkles')
            ->emptyStateHeading('No breeds found')
            ->emptyStateDescription('Once you add your first breed, it will appear here.')
            ->deferLoading()
            ->defaultSort('name', 'asc')
            ->groups([
                Tables\Grouping\Group::make('categoryAnimals.name')
                    ->label('Category')
                    ->collapsible(),
            ])
            ->groupRecordsTriggerAction(
                fn(Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('Group records'),
            );
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\AnimalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBreeds::route('/'),
            'create' => Pages\CreateBreeds::route('/create'),
            // 'edit' => Pages\EditBreeds::route('/{record}/edit'),
            // 'view' => Pages\ViewBreeds::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
