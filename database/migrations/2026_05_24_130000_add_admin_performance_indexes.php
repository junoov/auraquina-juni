<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
            $table->index('created_at');
        });

        Schema::table('varian_produks', function (Blueprint $table) {
            $table->index('stok');
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->index('aktif');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('varian_produks', function (Blueprint $table) {
            $table->dropIndex(['stok']);
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->dropIndex(['aktif']);
        });
    }
};
