<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenueResource\Pages;
use App\Models\Schedule;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VenueResource extends Resource
{
    protected static ?string $model = Venue::class;
    protected static ?string $navigationIcon  = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Venues';
    protected static ?int    $navigationSort  = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Venue Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()->maxLength(100)->columnSpan(2),

                    Forms\Components\Select::make('type')
                        ->options([
                            'stadium'      => 'Stadium',
                            'turf_indoor'  => 'Indoor Turf',
                            'turf_outdoor' => 'Outdoor Turf',
                            'vip_box'      => 'VIP Box',
                            'hall'         => 'Hall',
                        ])->required(),

                    Forms\Components\TextInput::make('capacity')
                        ->numeric()->minValue(1),

                    Forms\Components\Textarea::make('description')
                        ->rows(3)->columnSpan(2),
                ]),

            Forms\Components\Section::make('Pricing & Settings')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('hourly_rate')
                        ->label('Price per Hour (৳)')
                        ->numeric()->required()->prefix('৳'),

                    Forms\Components\ColorPicker::make('color')
                        ->label('Calendar Color'),

                    Forms\Components\Select::make('schedule_id')
                        ->label('Schedule')
                        ->relationship('schedule', 'name')
                        ->searchable()->preload(),

                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()->default(0),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')->label(''),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable()->weight('bold'),

                Tables\Columns\BadgeColumn::make('type')
                    ->formatStateUsing(fn($state) => ucfirst(str_replace('_', ' ', $state)))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()->sortable(),

                Tables\Columns\TextColumn::make('hourly_rate')
                    ->label('Price/Hour')
                    ->money('BDT')->sortable(),

                Tables\Columns\TextColumn::make('schedule.name')
                    ->label('Schedule')->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'stadium'      => 'Stadium',
                        'turf_indoor'  => 'Indoor Turf',
                        'turf_outdoor' => 'Outdoor Turf',
                        'vip_box'      => 'VIP Box',
                        'hall'         => 'Hall',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->reorderable('sort_order')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'edit'   => Pages\EditVenue::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}
