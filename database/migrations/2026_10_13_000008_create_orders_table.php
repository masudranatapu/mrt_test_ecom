<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->nullable()->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();

            $table->string('status', 20)->default('pending')->index();
            $table->string('payment_method', 20);
            $table->string('payment_status', 20)->default('unpaid')->index();

            // Shipping address is copied so later address edits don't change old orders
            $table->string('shipping_name');
            $table->string('shipping_phone', 20);
            $table->string('shipping_address');
            $table->string('shipping_area')->nullable();
            $table->string('shipping_city', 100);
            $table->string('shipping_postal_code', 20)->nullable();
            $table->string('shipping_zone', 20);

            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('shipping_charge', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            $table->text('notes')->nullable();
            $table->date('due_date')->nullable(); // 28-day credit orders
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            // Snapshot of the product at order time
            $table->string('product_name');
            $table->string('sku', 64);
            $table->string('unit', 30);
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20);
            $table->string('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // admin, null = customer/system
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
