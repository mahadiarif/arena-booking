<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blackout_periods', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->foreignId('schedule_id')
                  ->nullable()
                  ->constrained('schedules')
                  ->nullOnDelete();
            $table->foreignId('venue_id')
                  ->nullable()
                  ->constrained('venues')
                  ->nullOnDelete();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->boolean('repeats_annually')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(
                ['schedule_id', 'start_datetime', 'end_datetime'],
                'idx_blackout_schedule_range'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blackout_periods');
    }
};
