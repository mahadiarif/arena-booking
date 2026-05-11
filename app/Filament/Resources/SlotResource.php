<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SlotResource\Pages;
use App\Models\Slot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SlotResource extends Resource
{
    protected static ?string $model = Slot::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Bookings';
    protected static ?int    $navigationSort  = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Slot Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('venue_id')
                        ->relationship('venue', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('schedule_id')
                        ->relationship('schedule', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\DatePicker::make('date')
                        ->required()
                        ->default(now()),

                    Forms\Components\TextInput::make('label')
                        ->placeholder('e.g. Morning Special')
                        ->maxLength(100),

                    Forms\Components\TimePicker::make('start_time')
                        ->required()
                        ->seconds(false),

                    Forms\Components\TimePicker::make('end_time')
                        ->required()
                        ->seconds(false),
                ]),

            Forms\Components\Section::make('Availability')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'available' => 'Available',
                            'booked'    => 'Booked',
                            'blocked'   => 'Blocked',
                            'maintenance'=> 'Maintenance',
                        ])
                        ->required()
                        ->default('available'),

                    Forms\Components\TextInput::make('max_bookings')
                        ->label('Max Participants')
                        ->numeric()
                        ->default(1)
                        ->required(),

                    Forms\Components\TextInput::make('current_bookings')
                        ->label('Current Bookings')
                        ->numeric()
                        ->default(0)
                        ->disabled(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('venue.name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Time')
                    ->formatStateUsing(fn($record) => $record->start_time_formatted . ' - ' . $record->end_time_formatted),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'booked',
                        'danger'  => 'blocked',
                        'gray'    => 'maintenance',
                    ]),

                Tables\Columns\TextColumn::make('current_bookings')
                    ->label('Usage')
                    ->formatStateUsing(fn($record) => "{$record->current_bookings}/{$record->max_bookings}")
                    ->sortable(),

                Tables\Columns\TextColumn::make('label')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('venue_id')
                    ->label('Venue')
                    ->relationship('venue', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'booked'    => 'Booked',
                        'blocked'   => 'Blocked',
                    ]),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('block')
                    ->label('Block')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'available')
                    ->action(fn($record) => $record->update(['status' => 'blocked'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlots::route('/'),
            'create' => Pages\CreateSlot::route('/create'),
            'edit' => Pages\EditSlot::route('/{record}/edit'),
        ];
    }
}
