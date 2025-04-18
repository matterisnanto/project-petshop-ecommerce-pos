<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BrandResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BrandResource\RelationManagers;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Brand';

    protected static ?string $modelLabel = 'Brand';

    protected static ?string $pluralModelLabel = 'Brand';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Product Resource';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Brand Information')
                    ->description('Enter your brand details')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Brand Name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Brand::generateUniqueSlug($state));
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
                                    ->maxLength(255)
                                    ->helperText('Will be auto-generated from name')
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
                            ->imagePreviewHeight('150')
                            ->maxSize(1024)
                            ->required()
                            ->helperText('Upload a square logo (max 1MB)')
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
                Tables\Columns\ImageColumn::make('logo'),
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            // 'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
