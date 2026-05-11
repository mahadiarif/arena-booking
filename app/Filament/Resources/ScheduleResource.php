<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?int    $navigationSort  = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Schedule Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')->required()->maxLength(100)->columnSpan(2),

                    Forms\Components\TimePicker::make('start_time')->required()->seconds(false),
                    Forms\Components\TimePicker::make('end_time')->required()->seconds(false),

                    Forms\Components\Select::make('slot_interval_minutes')
                        ->label('Slot Duration')
                        ->options([
                            30  => '30 minutes',
                            60  => '1 hour',
                            90  => '1.5 hours',
                            120 => '2 hours',
                            180 => '3 hours',
                        ])->required(),

                    Forms\Components\Select::make('timezone')
                        ->options(collect(timezone_identifiers_list())->mapWithKeys(fn($t) => [$t => $t]))
                        ->searchable()
                        ->default('Asia/Dhaka'),
                ]),

            Forms\Components\Section::make('Working Days')
                ->schema([
                    Forms\Components\CheckboxList::make('allowed_days')
                        ->options([
                            'Sun' => 'Sunday',
                            'Mon' => 'Monday',
                            'Tue' => 'Tuesday',
                            'Wed' => 'Wednesday',
                            'Thu' => 'Thursday',
                            'Fri' => 'Friday',
                            'Sat' => 'Saturday',
                        ])
                        ->columns(7)
                        ->required(),
                ]),

            Forms\Components\Section::make('Availability')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('availability_start')->label('From'),
                    Forms\Components\DatePicker::make('availability_end')->label('Until'),
                    Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('start_time')->label('Open'),
                Tables\Columns\TextColumn::make('end_time')->label('Close'),
                Tables\Columns\TextColumn::make('slot_interval_minutes')->label('Slot (min)'),
                Tables\Columns\TextColumn::make('timezone')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
                Tables\Columns\TextColumn::make('venues_count')->label('Venues')->counts('venues')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit'   => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
