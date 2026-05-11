<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditTransactionResource\Pages;
use App\Models\CreditTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CreditTransactionResource extends Resource
{
    protected static ?string $model = CreditTransaction::class;
    protected static ?string $navigationIcon  = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Customers';
    protected static ?int    $navigationSort  = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Transaction Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('customer_id')
                        ->relationship('customer', 'name')
                        ->searchable()->required(),

                    Forms\Components\Select::make('type')
                        ->options([
                            'credit' => 'Credit (Add Money)',
                            'debit'  => 'Debit (Use Money)',
                        ])->required(),

                    Forms\Components\TextInput::make('amount')
                        ->numeric()->required()->prefix('৳'),

                    Forms\Components\TextInput::make('reason')
                        ->required()->maxLength(255)->columnSpan(2),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')->searchable()->sortable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('BDT')
                    ->color(fn($record) => $record->type === 'credit' ? 'success' : 'danger'),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'success' => 'credit',
                        'danger'  => 'debit',
                    ]),

                Tables\Columns\TextColumn::make('reason')->wrap(),

                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'credit' => 'Credit',
                        'debit'  => 'Debit',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditTransactions::route('/'),
            'create' => Pages\CreateCreditTransaction::route('/create'),
        ];
    }
}
