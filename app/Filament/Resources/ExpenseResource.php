<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Expense;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Forms\Components\TextInput\Mask;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ExpenseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ExpenseResource\RelationManagers;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationLabel = 'Expense';

    protected static ?string $modelLabel = 'Expense';

    protected static ?string $pluralModelLabel = 'Expense';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Offers & Payments';
    protected static ?int $navigationSort = 16;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Expense Details')
                    ->description('Input the details of your spending below.')
                    ->icon('heroicon-o-receipt-percent') // hanya jika kamu pakai Section yang support icon()
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Expense Name')
                            ->placeholder('e.g. Office Rent, Internet, Utilities')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-pencil-square')
                            ->columnSpanFull()
                            ->helperText('Name this expense for future reference.'),

                        Forms\Components\Textarea::make('note')
                            ->label('Notes')
                            ->placeholder('e.g. Paid for 3 months in advance')
                            ->required()
                            ->autosize()
                            ->columnSpanFull()
                            ->rows(3)
                            ->helperText('Provide additional info for clarity.'),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->columnSpanFull()
                            ->helperText('Use whole numbers, e.g. 1000000 for one million.'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        // Format nilai sebagai Rupiah
                        return 'Rp ' . number_format($state, 0, ',', '.');
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            // 'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
