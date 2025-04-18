<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryGroomingResource\Pages;
use App\Filament\Resources\CategoryGroomingResource\RelationManagers;
use App\Models\CategoryGrooming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryGroomingResource extends Resource
{
    protected static ?string $model = CategoryGrooming::class;

    protected static ?string $navigationLabel = 'Category Grooming';
    protected static ?string $modelLabel = 'Category Grooming';
    protected static ?string $pluralModelLabel = 'Category Grooming';
    protected static ?string $navigationGroup = 'Service Resource';
    protected static ?int $navigationSort = 10;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Enter the basic details of the item')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter a descriptive name')
                            ->columnSpan(['md' => 2]),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('auto-generated-if-empty')
                            ->hint('Will be automatically generated from name if left empty')
                            ->rules(['alpha_dash'])
                            ->columnSpan(['md' => 2]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(5)
                            ->placeholder('Enter a detailed description')
                            ->maxLength(1000)
                            ->helperText('Max 1000 characters'),
                    ]),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label('Upload Photo')
                            ->directory('photos')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imagePreviewHeight('250')
                            ->maxSize(2048)
                            ->helperText('Maximum file size: 2MB')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('photo')
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCategoryGroomings::route('/'),
            'create' => Pages\CreateCategoryGrooming::route('/create'),
            'edit' => Pages\EditCategoryGrooming::route('/{record}/edit'),
        ];
    }
}
