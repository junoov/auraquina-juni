<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('varian_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->string('ukuran'); // XS, S, M, L, XL
            $table->string('warna');
            $table->string('kode_warna')->nullable(); // hex color
            $table->string('sku')->unique();
            $table->integer('stok')->default(0);
            $table->integer('penyesuaian_harga')->default(0); // tambahan/pengurangan harga
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('varian_produks');
    }
};
