<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('label', 100)->nullable();
            $table->enum('status', ['available', 'partial', 'booked', 'blocked'])->default('available');
            $table->unsignedTinyInteger('max_bookings')->default(1);
            $table->unsignedTinyInteger('current_bookings')->default(0);
            $table->timestamps();

            $table->unique(['venue_id', 'date', 'start_time'], 'uq_venue_date_start');
            $table->index(['venue_id', 'date', 'status'], 'idx_venue_date_status');
            $table->index(['schedule_id', 'date', 'status'], 'idx_schedule_date_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
