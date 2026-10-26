<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adoption_listings', function (Blueprint $table): void {
            $table->json('gallery_paths')->nullable()->after('photo_path');
            $table->text('health_notes')->nullable()->after('description');
            $table->text('vaccination_notes')->nullable()->after('is_vaccinated');
        });

        Schema::create('adoption_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('adoption_listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 20);
            $table->text('message');
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->unique(['adoption_listing_id', 'user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adoption_applications');
        Schema::table('adoption_listings', function (Blueprint $table): void {
            $table->dropColumn(['gallery_paths', 'health_notes', 'vaccination_notes']);
        });
    }
};
