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

    protected static ?int $navigationSort = 7;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment method Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(['md' => 2]),
                        Forms\Components\TextInput::make('account_number')
                            ->label('Account Number')
                            ->required()
                            ->maxLength(50)
                            ->columnSpan(['md' => 2]),
                        Forms\Components\FileUpload::make('image')
                            ->label('Upload Image')
                            ->image()
                            ->directory('expense-images')
                            ->imageEditor()
                            ->required()
                            ->columnSpan(['md' => 2]),
                        Forms\Components\Toggle::make('olshop_transaction')
                            ->label('this is online payment?')
                            ->inline(false)
                            ->required(),
                        Forms\Components\Toggle::make('pos_transaction')
                            ->label('this is online payment?')
                            ->inline(false)
                            ->required(),
                        Forms\Components\Toggle::make('is_cash')
                            ->label('is this cash payment?')
                            ->inline(false)
                            ->required(),
                    ])
                    ->columns(2) // Mengatur layout menjadi 2 kolom
                    ->collapsible() // Membuat section bisa di-collapse
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
                Tables\Columns\ToggleColumn::make('is_cash')
                    ->label('Cash Payment')
                    ->onColor('success')
                    ->offColor('danger')
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title('Cash Payment Status Updated')
                            ->body("Cash Payment status changed to " . ($state ? 'Active' : 'Inactive'))
                            ->success()
                            ->send();
                    }),
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
