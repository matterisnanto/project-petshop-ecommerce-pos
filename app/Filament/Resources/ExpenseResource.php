<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Expense;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
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

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Expense Details')
                    ->description('Input the details of your spending below.')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->collapsible()
                    ->compact()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Expense Name')
                            ->placeholder('e.g. Office Rent, Internet, Utilities')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-tag')
                            ->columnSpanFull()
                            ->helperText('Name this expense for future reference.')
                            ->hint('Max 255 characters')
                            ->hintIcon('heroicon-o-information-circle')
                            ->extraAttributes(['class' => 'focus:ring-2 focus:ring-primary-500']),
    
                        Forms\Components\Textarea::make('note')
                            ->label('Notes')
                            ->placeholder('e.g. Paid for 3 months in advance')
                            ->required()
                            ->autosize()
                            ->columnSpanFull()
                            ->rows(3)
                            ->helperText('Provide additional info for clarity.')
                            ->hint('Optional details')
                            ->extraAttributes([
                                'class' => 'mt-2 focus:ring-2 focus:ring-primary-500 min-h-[100px]'
                            ]),
    
                        // Amount and Date side by side
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->prefixIcon('heroicon-o-banknotes')
                            ->columnSpan(['md' => 1])
                            ->helperText('Enter amount in Rupiah')
                            ->extraAttributes(['class' => 'focus:ring-2 focus:ring-primary-500']),
    
                        Forms\Components\DatePicker::make('date_expense')
                            ->label('Expense Date')
                            ->required()
                            ->default(now())
                            ->displayFormat('d M Y')
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar')
                            ->columnSpan(['md' => 1])
                            ->helperText('Transaction date')
                            ->extraAttributes(['class' => 'focus:ring-2 focus:ring-primary-500']),
                    ])
                    ->extraAttributes(['class' => 'shadow-sm rounded-lg border border-gray-200']),
            ])
            ->columns(1);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Expense Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignCenter()
                    ->description(fn ($record) => Str::limit($record->note, 30))
                    ->wrap(),
    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->sortable()
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->alignCenter()
                    ->color(fn ($record) => $record->amount > 1000000 ? 'danger' : 'success')
                    ->description('Total Expense')
                    ->icon(fn ($record) => $record->amount > 1000000 ? 'heroicon-o-exclamation-triangle' : null),
    
                Tables\Columns\TextColumn::make('date_expense')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable()
                    ->alignCenter()
                    ->color('primary'),
    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignEnd(),
    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignEnd(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('large_expenses')
                    ->label('Large Expenses')
                    ->options([
                        'yes' => 'Above Rp 1.000.000',
                        'no' => 'Below Rp 1.000.000',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['value'] === 'yes',
                            fn($query) => $query->where('amount', '>', 1000000),
                            fn($query) => $query->where('amount', '<=', 1000000)
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->color('primary') // Blue color
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
                        ->label('Delete Selected')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('date_expense', 'desc')
            ->striped()
            ->deferLoading()
            ->emptyStateHeading('No expenses recorded yet')
            ->emptyStateDescription('Create your first expense to get started')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Expense')
                    ->icon('heroicon-o-plus'),
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