<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestBookings extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_ref')
                    ->label('Reference')
                    ->fontFamily('mono')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer'),

                Tables\Columns\TextColumn::make('venue.name')
                    ->label('Venue')
                    ->badge(),

                Tables\Columns\TextColumn::make('slot.date')
                    ->label('Date')
                    ->date(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('BDT'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'checked_in',
                        'gray'    => 'completed',
                        'danger'  => fn($state) => in_array($state, ['cancelled', 'no_show']),
                    ]),
            ]);
    }
}
