<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['vaccination', 'deworming', 'weight', 'checkup', 'other']);
            $table->date('record_date');
            $table->date('next_due_date')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->text('note')->nullable();
            // Set when a reminder email has been sent, so the scheduler never sends duplicates.
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();

            $table->index(['pet_id', 'record_date']);
            $table->index(['next_due_date', 'reminded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_health_records');
    }
};
