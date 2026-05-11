<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug')->unique();
            $table->enum('type', ['stadium', 'turf_indoor', 'turf_outdoor', 'vip_box', 'hall']);
            $table->unsignedSmallInteger('capacity');
            $table->string('color', 7)->default('#185FA5');
            $table->decimal('hourly_rate', 10, 2);
            $table->unsignedSmallInteger('min_duration_minutes')->default(60);
            $table->unsignedSmallInteger('max_duration_minutes')->default(240);
            $table->boolean('requires_approval')->default(false);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
