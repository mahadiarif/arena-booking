<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_reviews', function (Blueprint $col) {
            $col->id();
            $col->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $col->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $col->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $col->unsignedTinyInteger('rating')->default(5)->comment('1 to 5');
            $col->text('comment')->nullable();
            $col->boolean('is_published')->default(true);
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_reviews');
    }
};
