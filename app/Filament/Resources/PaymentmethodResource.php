<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Paymentmethod;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentmethodResource\Pages;
use App\Filament\Resources\PaymentmethodResource\RelationManagers;

class PaymentmethodResource extends Resource
{
    protected static ?string $model = Paymentmethod::class;

    protected static ?string $navigationLabel = 'Payment Method';

    protected static ?string $modelLabel = 'Payment Method';

    protected static ?string $pluralModelLabel = 'Payment Method';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Offers & Payments';

    protected static ?int $navigationSort = 17;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Method Configuration')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->description('Set up your payment method details and preferences')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                         // Basic Information Section
                         Forms\Components\Section::make('Basic Information')
                         ->icon('heroicon-o-information-circle')
                         ->schema([
                             Forms\Components\TextInput::make('name')
                                 ->label('Display Name')
                                 ->required()
                                 ->maxLength(255)
                                 ->placeholder('e.g. Bank Transfer, PayPal, Cash')
                                 ->prefixIcon('heroicon-o-tag')
                                 ->columnSpanFull()
                                 ->helperText('This name will be shown to customers'),
 
                             Forms\Components\TextInput::make('account_number')
                                 ->label('Account/Reference Number')
                                 ->maxLength(50)
                                 ->placeholder('e.g. 1234567890')
                                 ->prefixIcon('heroicon-o-identification')
                                 ->columnSpanFull()
                                 ->helperText('Required for bank transfers or similar methods'),
                         ])
                         ->columnSpan(['lg' => 1]),
                        
                        // Visual Identity Section
                        Forms\Components\Section::make('Visual Identity')
                            ->icon('heroicon-o-photo')
                            ->description('Branding and display settings')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Payment Logo')
                                    ->image()
                                    ->directory('payment-method-images')
                                    ->imageEditor()
                                    ->imageResizeMode('contain')
                                    ->imagePreviewHeight('120')
                                    ->panelAspectRatio('1:1')
                                    ->panelLayout('integrated')
                                    ->required()
                                    ->columnSpanFull()
                                    ->helperText('Upload a square logo (1:1 ratio) for this payment method'),
                            ])
                            ->columnSpan(['lg' => 1]),
    
    
                        // Payment Type Settings
                        Forms\Components\Section::make('Payment Type Settings')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->description('Configure where this payment method can be used')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Toggle::make('olshop_transaction')
                                            ->label('Online Payments')
                                            ->inline(false)
                                            ->onColor('primary')
                                            ->offColor('gray')
                                            ->helperText('Enable for e-commerce transactions')
                                            ->required(),
    
                                        Forms\Components\Toggle::make('pos_transaction')
                                            ->label('Point of Sale')
                                            ->inline(false)
                                            ->onColor('warning')
                                            ->offColor('gray')
                                            ->helperText('Enable for in-person/store payments')
                                            ->required(),
    
                                        Forms\Components\Toggle::make('is_cash')
                                            ->label('Cash Payment')
                                            ->inline(false)
                                            ->onColor('success')
                                            ->offColor('gray')
                                            ->helperText('Mark as cash payment method')
                                            ->required(),
                                    ])
                                    ->columns(3),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image'),
                    Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->account_number ? 'No. Rek: '.$record->account_number : null),
    
               // Tables\Columns\TextColumn::make('account_number')
                   // ->label('Account Number'),
                Tables\Columns\ToggleColumn::make('olshop_transaction')
                    ->label('Olshop')
                    ->onColor('success')
                    ->offColor('danger')
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title('Online Store Payment Status Updated')
                            ->body("Online Store Payment {$record->name} status changed to " . ($state ? 'Active' : 'Inactive'))
                            ->success()
                            ->send();
                    }),
                Tables\Columns\ToggleColumn::make('pos_transaction')
                    ->label('POS')
                    ->onColor('success')
                    ->offColor('danger')
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title('POS Payment Status Updated')
                            ->body("POS Payment {$record->name} status changed to " . ($state ? 'Active' : 'Inactive'))
                            ->success()
                            ->send();
                    }),
                // Tables\Columns\ToggleColumn::make('is_cash')
                //     ->label('Cash Payment')
                //     ->onColor('success')
                //     ->offColor('danger')
                //     ->afterStateUpdated(function ($record, $state) {
                //         Notification::make()
                //             ->title('Cash Payment Status Updated')
                //             ->body("Cash Payment status changed to " . ($state ? 'Active' : 'Inactive'))
                //             ->success()
                //             ->send();
                //     }),
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
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    
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
            'index' => Pages\ListPaymentmethods::route('/'),
            'create' => Pages\CreatePaymentmethod::route('/create'),
            // 'edit' => Pages\EditPaymentmethod::route('/{record}/edit'),
        ];
    }
}