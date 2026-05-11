<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->enum('channel', ['email', 'sms']);
            $table->enum('event', [
                'booking_confirmed',
                'booking_reminder_24h',
                'booking_cancelled',
                'payment_received',
                'waitlist_notified',
            ]);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['customer_id', 'channel', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
