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
        Schema::create('personal_trainer_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('scheduled_at');
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'missed'])->default('scheduled');
            $table->time('check_in_time');
            $table->time('check_out_time')->nullable();
            $table->json('training_log')->nullable();
            $table->text('trainer_notes')->nullable();
            $table->text('member_feedback')->nullable();
            $table->foreignId('personal_trainer_assignment_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_trainer_schedules');
    }
};
