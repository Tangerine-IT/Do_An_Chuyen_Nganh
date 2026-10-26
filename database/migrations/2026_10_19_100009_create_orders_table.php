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
            // Orders are financial records: they outlive the customer account and the coupon.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number', 32)->unique();

            $table->enum('status', ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['cod', 'vnpay', 'momo'])->default('cod');

            // Money: whole VND amounts. total = subtotal - discount_total + shipping_fee.
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('discount_total')->default(0);
            $table->unsignedBigInteger('shipping_fee')->default(0);
            $table->unsignedBigInteger('total');

            $table->string('recipient_name');
            $table->string('recipient_phone', 20);
            $table->string('shipping_address');
            $table->string('shipping_ward')->nullable();
            $table->string('shipping_district')->nullable();
            $table->string('shipping_province');
            $table->text('note')->nullable();

            $table->timestamp('placed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('payment_status');
            $table->index('placed_at');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // The product may be deleted later; the snapshot columns below keep the order readable.
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku', 64)->nullable();
            $table->unsignedBigInteger('unit_price');
            $table->unsignedSmallInteger('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
