<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Promocode;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PromocodeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PromocodeResource\RelationManagers;

class PromocodeResource extends Resource
{
    protected static ?string $model = Promocode::class;

    protected static ?string $navigationLabel = 'Promo Code';

    protected static ?string $modelLabel = 'Promo Code';

    protected static ?string $pluralModelLabel = 'Promo Code';

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';

    protected static ?string $navigationGroup = 'Offers & Payments';

    protected static ?int $navigationSort = 16;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Promo Code Information')
                    ->icon('heroicon-o-percent-badge')  // Changed to sparkles icon for promotions
                    ->description('Create a new discount promotion')
                    ->columns(2)
                    ->collapsible()  // Added collapsible feature
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Promo Code')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. SUMMER20')
                            ->columnSpan(['md' => 1])
                            ->helperText('Enter uppercase letters and numbers only')
                            ->prefixIcon('heroicon-o-tag')  // Added tag icon
                            ->hint('Max 255 characters'),
    
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Discount Value')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->maxValue(10000000)
                            ->step(1000)
                            ->columnSpan(['md' => 1])
                            ->helperText('Enter the discount amount in Rupiah')
                            ->hint('Max Rp 10,000,000')
                            ->hintColor('danger'),
    
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->prefixIcon('heroicon-o-calendar')  // Changed to play icon
                            ->columnSpan(['md' => 1])
                            ->helperText('When the promo becomes active'),
    
                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->minDate(fn(Get $get) => $get('start_date') ?: now())
                            ->prefixIcon('heroicon-o-calendar')  // Changed to stop icon
                            ->columnSpan(['md' => 1])
                            ->helperText('When the promo expires'),
    
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Status')
                            ->required()
                            ->inline(false)
                            ->onIcon('heroicon-o-check')
                            ->offIcon('heroicon-o-x-mark')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->columnSpan(['md' => 2])
                            ->helperText('Toggle to activate/deactivate the promo'),
                    ]),
            ])
            ->columns(2);  // Added form columns for better layout
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->label('PROMO CODE')
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('discount_amount')
                    ->numeric()
                    ->sortable()
                    ->label('DISCOUNT')
                    ->money('IDR', locale: 'id')
                    ->color('success')
                    ->weight('bold')
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->sortable()
                    ->label('START DATE')
                    ->dateTime('d M Y')
                    ->alignCenter()
                    ->description(fn ($record) => $record->end_date 
                        ? 'Until: '.Carbon::parse($record->end_date)->format('d M Y') 
                        : null),
                
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('STATUS')
                    ->onColor('success')
                    ->offColor('danger')
                    ->alignCenter()
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title('Promo Code Status Updated')
                            ->body("Promo code <strong>{$record->code}</strong> " . ($state ? 'has been activated' : 'has been deactivated'))
                            ->success()
                            ->send();
                    })
                    ->extraAttributes(['class' => 'px-4']),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('CREATED AT')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('UPDATED AT')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('DELETED AT')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\ViewAction::make()
                    //->iconButton()
                    //->color('primary') // Blue color
                    //->tooltip('View'),
                    
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
            ->defaultSort('start_date', 'desc')
            ->emptyStateHeading('No promo codes yet')
            ->emptyStateDescription('Create your first promo code')
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
            'index' => Pages\ListPromocodes::route('/'),
            'create' => Pages\CreatePromocode::route('/create'),
            // 'edit' => Pages\EditPromocode::route('/{record}/edit'),
        ];
    }
}