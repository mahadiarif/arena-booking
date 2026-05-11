<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('method', [
                'cash',
                'bkash',
                'nagad',
                'rocket',
                'bank_transfer',
                'cheque',
                'credit',
            ]);
            $table->string('reference_no', 100)->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->timestamp('paid_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
