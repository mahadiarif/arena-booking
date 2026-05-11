<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Bookings';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Payment Info')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('booking_id')
                        ->relationship('booking', 'booking_ref')
                        ->searchable()->required(),

                    Forms\Components\TextInput::make('amount')
                        ->numeric()->required()->prefix('৳'),

                    Forms\Components\Select::make('payment_method')
                        ->options([
                            'cash'    => 'Cash',
                            'bkash'   => 'bKash',
                            'nagad'   => 'Nagad',
                            'card'    => 'Card',
                            'wallet'  => 'Wallet Credit',
                        ])->required(),

                    Forms\Components\Select::make('status')
                        ->options([
                            'pending'   => 'Pending',
                            'completed' => 'Completed',
                            'failed'    => 'Failed',
                            'refunded'  => 'Refunded',
                        ])->required(),

                    Forms\Components\TextInput::make('transaction_id')
                        ->label('Transaction ID'),

                    Forms\Components\DateTimePicker::make('paid_at')
                        ->default(now()),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking.booking_ref')
                    ->label('Booking Ref')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('BDT')->sortable(),

                Tables\Columns\BadgeColumn::make('payment_method')
                    ->colors([
                        'primary' => 'bkash',
                        'success' => 'nagad',
                        'gray'    => 'cash',
                        'info'    => 'card',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger'  => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'completed' => 'Completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
