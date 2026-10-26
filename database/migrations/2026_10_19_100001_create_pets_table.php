<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('species', ['dog', 'cat', 'bird', 'fish', 'small_pet', 'other'])->index();
            $table->string('breed')->nullable();
            $table->date('birthday')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->enum('sex', ['male', 'female', 'unknown'])->default('unknown');
            $table->json('allergies')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
