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

    protected static ?int $navigationSort = 15;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Method Details')
                    ->icon('heroicon-o-credit-card')  // Added icon
                    ->description('Configure payment method information')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        // Name Field
                        Forms\Components\TextInput::make('name')
                            ->label('Payment Method Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Bank Transfer, E-Wallet, Cash')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->columnSpan(['md' => 2])
                            ->helperText('Enter the display name for this payment method'),

                        // Account Number Field
                        Forms\Components\TextInput::make('account_number')
                            ->label('Account/Reference Number')
                            ->maxLength(50)
                            ->placeholder('e.g. 1234567890')
                            ->columnSpan(['md' => 2])
                            ->helperText('Optional account or reference number'),

                        // Image Upload
                        Forms\Components\FileUpload::make('image')
                            ->label('Payment Method Logo')
                            ->image()
                            ->directory('payment-method-images')
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->imagePreviewHeight('120')
                            ->panelAspectRatio('1:1')
                            ->panelLayout('integrated')
                            ->required()
                            ->columnSpan(['md' => 2])
                            ->helperText('Upload the payment method logo/icon (1:1 ratio recommended)'),

                        // Payment Type Toggles
                        Forms\Components\Fieldset::make('Payment Type')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Toggle::make('olshop_transaction')
                                    ->label('Online Payment')
                                    ->inline(false)
                                    ->onColor('primary')
                                    ->offColor('gray')
                                    ->required(),

                                Forms\Components\Toggle::make('pos_transaction')
                                    ->label('Offline Payment')
                                    ->inline(false)
                                    ->onColor('warning')
                                    ->offColor('gray')
                                    ->required(),

                                Forms\Components\Toggle::make('is_cash')
                                    ->label('Cash Payment')
                                    ->inline(false)
                                    ->onColor('success')
                                    ->offColor('gray')
                                    ->required(),
                            ])
                            ->columnSpan(['md' => 2]),
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
                    ->label('Name'),
                Tables\Columns\TextColumn::make('account_number')
                    ->label('Account Number'),
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
            'index' => Pages\ListPaymentmethods::route('/'),
            'create' => Pages\CreatePaymentmethod::route('/create'),
            // 'edit' => Pages\EditPaymentmethod::route('/{record}/edit'),
        ];
    }
}
