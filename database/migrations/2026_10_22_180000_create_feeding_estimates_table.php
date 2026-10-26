<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feeding_estimates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('package_size_grams');
            $table->decimal('daily_grams', 8, 2);
            $table->date('runs_out_at');
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();
            $table->index('runs_out_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feeding_estimates');
    }
};
