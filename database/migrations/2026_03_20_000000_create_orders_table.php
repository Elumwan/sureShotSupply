<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('stripe_session_id')->unique()->nullable();
            $table->string('stripe_payment_intent')->nullable();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->unsignedInteger('total');
            $table->json('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
