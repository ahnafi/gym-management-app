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
        Schema::create('personal_trainers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nickname');
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('images')->nullable();
            $table->foreignId('user_personal_trainer_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_trainer');
    }
};
