<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurrence_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['daily', 'weekly', 'monthly']);
            $table->unsignedTinyInteger('interval')->default(1);
            $table->json('days_of_week')->nullable();
            $table->enum('end_type', ['on_date', 'after_count']);
            $table->date('end_date')->nullable();
            $table->unsignedSmallInteger('end_after_count')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurrence_rules');
    }
};
