<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicate = DB::table('reviews')
            ->select('pesanan_id', 'produk_id')
            ->groupBy('pesanan_id', 'produk_id')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new \RuntimeException('Migration dibatalkan: terdapat ulasan ganda untuk pesanan dan produk yang sama.');
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('user_id', 'reviews_user_id_index');
            $table->dropUnique('reviews_user_id_produk_id_unique');
            $table->unique(['pesanan_id', 'produk_id']);
        });
    }

    public function down(): void
    {
        $duplicate = DB::table('reviews')
            ->select('user_id', 'produk_id')
            ->groupBy('user_id', 'produk_id')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new \RuntimeException('Rollback dibatalkan: satu akun memiliki beberapa ulasan untuk produk yang sama.');
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_pesanan_id_produk_id_unique');
            $table->dropIndex('reviews_user_id_index');
            $table->unique(['user_id', 'produk_id']);
        });
    }
};
