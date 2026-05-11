<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->timestamps();
        });

        Schema::create('resource_group_venue', function (Blueprint $table) {
            $table->foreignId('resource_group_id')
                  ->constrained('resource_groups')
                  ->cascadeOnDelete();
            $table->foreignId('venue_id')
                  ->constrained('venues')
                  ->cascadeOnDelete();
            $table->primary(['resource_group_id', 'venue_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_group_venue');
        Schema::dropIfExists('resource_groups');
    }
};
