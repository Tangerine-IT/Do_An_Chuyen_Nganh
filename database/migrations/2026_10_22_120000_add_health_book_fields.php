<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pet_health_records', function (Blueprint $table): void {
            $table->string('attachment_path')->nullable()->after('note');
        });

        Schema::table('pets', function (Blueprint $table): void {
            $table->string('public_token')->nullable()->unique()->after('photo_path');
            $table->boolean('public_name')->default(true)->after('public_token');
            $table->boolean('public_photo')->default(true)->after('public_name');
            $table->boolean('public_vaccinations')->default(true)->after('public_photo');
            $table->boolean('public_contact')->default(false)->after('public_vaccinations');
        });
    }

    public function down(): void
    {
        Schema::table('pet_health_records', fn (Blueprint $table) => $table->dropColumn('attachment_path'));
        Schema::table('pets', function (Blueprint $table): void {
            $table->dropUnique(['public_token']);
            $table->dropColumn(['public_token', 'public_name', 'public_photo', 'public_vaccinations', 'public_contact']);
        });
    }
};
