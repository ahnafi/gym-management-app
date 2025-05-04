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
        Schema::create('gym_visits', function (Blueprint $table) {
            $table->id();
            $table->date('visit_date');
            $table->time('entry_time');
            $table->time('exit_time')->nullable();
            $table->enum('status', ['in_gym', 'left'])->default('in_gym');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_visits');
    }
};
