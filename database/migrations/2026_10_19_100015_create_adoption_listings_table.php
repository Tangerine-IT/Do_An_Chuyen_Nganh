<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adoption_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->enum('species', ['dog', 'cat', 'bird', 'fish', 'small_pet', 'other']);
            $table->string('breed')->nullable();
            $table->enum('sex', ['male', 'female', 'unknown'])->default('unknown');
            $table->unsignedSmallInteger('age_months')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_vaccinated')->default(false);
            $table->boolean('is_neutered')->default(false);
            $table->string('photo_path')->nullable();

            $table->string('contact_name');
            $table->string('contact_phone', 20);
            $table->string('city')->nullable();
            // Coordinates for the Leaflet map.
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->enum('status', ['available', 'pending', 'adopted'])->default('available');
            $table->timestamps();

            $table->index('species');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adoption_listings');
    }
};
