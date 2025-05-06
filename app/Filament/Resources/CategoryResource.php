<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

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
                Forms\Components\Section::make('Category Details')
                    ->description('Create or edit a product category')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Category Name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Category::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->placeholder('e.g., Cat Food, Dog Food, Toys')
                                    ->helperText('The display name for your category')
                                    ->columnSpan(['md' => 2]),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Identifier')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255)
                                    ->helperText('Auto-generated from category name')
                                    ->columnSpan(['md' => 2]),
                            ])
                            ->columns(2),

                        Forms\Components\FileUpload::make('icon')
                            ->label('Category Icon')
                            ->image()
                            ->directory('category-icons')
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->imageCropAspectRatio('1:1')
                            ->imagePreviewHeight('150')
                            ->maxSize(512)
                            ->helperText('Upload a square icon (recommended 200x200px)')
                            ->downloadable()
                            ->columnSpanFull()
                            ->panelAspectRatio('2:1'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            // 'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
