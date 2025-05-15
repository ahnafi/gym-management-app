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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('amount')->default(0);

            $table->string('payment_method')->nullable();
            $table->string('midtrans_transaction_id')->unique()->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('payment_token')->nullable();
            $table->string('redirect_url')->nullable();

            $table->datetime('payment_deadline');
            $table->datetime('payment_date')->nullable();
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'expired', 'failed', 'refunded'])->default('unpaid');

            $table->json('raw_notification')->nullable();

            $table->morphs('purchasable');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
