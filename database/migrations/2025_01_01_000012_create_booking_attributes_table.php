<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->enum('field_type', ['text', 'select', 'checkbox', 'textarea']);
            $table->json('options_json')->nullable();
            $table->boolean('is_required')->default(false);
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('booking_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                  ->constrained('bookings')
                  ->cascadeOnDelete();
            $table->foreignId('booking_attribute_id')
                  ->constrained('booking_attributes')
                  ->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(
                ['booking_id', 'booking_attribute_id'],
                'uq_booking_attribute_value'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_attribute_values');
        Schema::dropIfExists('booking_attributes');
    }
};
