<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gym_class_attendances', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['assigned', 'attended',  'missed', 'cancelled'])->default('assigned');
            $table->datetime('attended_at');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('gym_class_schedule_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_class_attendances');
    }
};
