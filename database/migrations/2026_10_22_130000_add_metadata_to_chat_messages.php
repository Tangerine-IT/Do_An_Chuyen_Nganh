<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table): void {
            $table->json('metadata')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', fn (Blueprint $table) => $table->dropColumn('metadata'));
    }
};
