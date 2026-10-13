<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per online payment attempt (SSLCommerz / bKash).
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway', 20);                         // sslcommerz | bkash
            $table->string('transaction_id', 64)->unique();       // our reference sent to the gateway
            $table->string('gateway_reference')->nullable();       // SSLCommerz sessionkey / bKash paymentID
            $table->string('gateway_transaction_id')->nullable();  // SSLCommerz val_id / bKash trxID
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('status', 20)->default('initiated');    // initiated | success | failed | cancelled
            $table->json('payload')->nullable();                   // last gateway response, for support
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
