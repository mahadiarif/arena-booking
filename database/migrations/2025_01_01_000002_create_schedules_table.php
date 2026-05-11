<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('timezone')->default('Asia/Dhaka');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_interval_minutes');
            $table->json('allowed_days');
            $table->boolean('allow_concurrent')->default(false);
            $table->unsignedTinyInteger('max_concurrent')->default(1);
            $table->date('availability_start')->nullable();
            $table->date('availability_end')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
