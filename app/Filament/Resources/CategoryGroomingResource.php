<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CategoryGrooming;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryGroomingResource\Pages;
use App\Filament\Resources\CategoryGroomingResource\RelationManagers;

class CategoryGroomingResource extends Resource
{
    protected static ?string $model = CategoryGrooming::class;

    protected static ?string $navigationLabel = 'Category Grooming';
    protected static ?string $modelLabel = 'Category Grooming';
    protected static ?string $pluralModelLabel = 'Category Grooming';
    protected static ?string $navigationGroup = 'Service Resource';
    protected static ?int $navigationSort = 14;

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
                        ->columnSpan(['md' => 2])
                        ->prefixIcon('heroicon-o-tag'), // Added icon
                    
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->placeholder('auto-generated-if-empty')
                        ->hint('Will be automatically generated from name if left empty')
                        ->rules(['alpha_dash'])
                        ->columnSpan(['md' => 2])
                        ->prefixIcon('heroicon-o-link'), // Added icon
                ])
                ->columns(2)
                ->collapsible(), // Added collapsible feature

            Forms\Components\Section::make('Description')
                ->icon('heroicon-o-document-text') // Added icon
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull()
                        ->rows(5)
                        ->placeholder('Enter a detailed description')
                        ->maxLength(1000)
                        ->helperText('Max 1000 characters')
                        ->extraInputAttributes(['class' => 'resize-none']), // Disable resize
                ])
                ->collapsible(), // Added collapsible feature

            Forms\Components\Section::make('Media')
                ->icon('heroicon-o-photo') // Added icon
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
                        ->helperText('Maximum file size: 2MB • Recommended ratio: 16:9')
                        ->downloadable() // Added download option
                        ->openable() // Added open option
                        ->columnSpanFull(),
                ])
                ->collapsible(), // Added collapsible feature
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\ImageColumn::make('photo')
                ->label('')
                ->circular()
                ->width(50)
                ->height(50)
                ->grow(false),
            
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->label('ITEM NAME')
                ->weight(FontWeight::Bold)
                ->description(fn ($record) => $record->slug)
                ->wrap(),
            
            Tables\Columns\TextColumn::make('created_at')
                ->label('CREATED')
                ->dateTime('d M Y')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            
            Tables\Columns\TextColumn::make('updated_at')
                ->label('UPDATED')
                ->dateTime('d M Y')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            
            Tables\Columns\TextColumn::make('deleted_at')
                ->label('DELETED')
                ->dateTime('d M Y')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\ViewAction::make()
                ->iconButton()
                ->color('primary')
                ->tooltip('View'),
                
            Tables\Actions\EditAction::make()
                ->iconButton()
                ->tooltip('Edit'),
                
            Tables\Actions\DeleteAction::make()
                ->iconButton()
                ->tooltip('Delete'),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Delete Selected'),
            ]),
        ])
        ->defaultSort('created_at', 'desc')
        ->emptyStateHeading('No items found')
        ->emptyStateDescription('Create your first item')
        ->striped()
        ->deferLoading();
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