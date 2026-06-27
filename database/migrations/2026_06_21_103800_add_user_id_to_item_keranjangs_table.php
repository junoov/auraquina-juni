<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_keranjangs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('session_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('item_keranjangs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
