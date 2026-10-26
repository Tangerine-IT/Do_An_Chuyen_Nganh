<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->unsignedInteger('package_size_grams')->nullable()->after('stock');
            $table->decimal('daily_grams_per_kg', 6, 2)->nullable()->after('package_size_grams');
        });
        Schema::table('loyalty_points', function (Blueprint $table): void {
            $table->foreignId('booking_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_points', fn (Blueprint $table) => $table->dropConstrainedForeignId('booking_id'));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['package_size_grams', 'daily_grams_per_kg']));
    }
};
