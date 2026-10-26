<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // A category that still has products cannot be deleted; a brand can (product keeps existing).
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku', 64)->nullable()->unique();
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();
            $table->text('ingredients')->nullable();

            // Money: whole VND amounts.
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('sale_price')->nullable();
            $table->unsignedInteger('stock')->default(0);

            // Recommendation targeting.
            $table->enum('species', ['dog', 'cat', 'bird', 'fish', 'small_pet', 'other', 'all'])->default('all');
            $table->enum('life_stage', ['young', 'adult', 'senior', 'all'])->nullable();
            $table->decimal('min_pet_weight_kg', 5, 2)->nullable();
            $table->decimal('max_pet_weight_kg', 5, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // SEO.
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();

            $table->timestamps();

            $table->index('species');
            $table->index(['is_active', 'species']);
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
