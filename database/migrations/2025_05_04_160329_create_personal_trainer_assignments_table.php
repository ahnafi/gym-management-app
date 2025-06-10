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
        Schema::create('personal_trainer_assignments', function (Blueprint $table) {
            $table->id();
            $table->integer('day_left');
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');

            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('personal_trainer_id')->constrained('users');
            $table->foreignId('personal_trainer_package_id')->constrained('personal_trainer_packages');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_trainer_assignments');
    }
};
