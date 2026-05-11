<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function afterFill(): void
    {
        if ($slotId = request()->query('slot_id')) {
            $slot = \App\Models\Slot::find($slotId);
            
            if ($slot) {
                $this->form->fill([
                    'venue_id' => $slot->venue_id,
                    'slot_id'  => $slot->id,
                    'total_amount' => $slot->venue->price ?? 0,
                ]);
            }
        }
    }
}
