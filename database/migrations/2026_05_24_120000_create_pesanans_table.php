<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan')->unique(); // e.g. AQ240524XXXX
            $table->string('session_id')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Status: pending_payment, paid, processing, shipped, delivered, completed, cancelled
            $table->string('status')->default('pending_payment');

            // Alamat pengiriman
            $table->string('nama_penerima');
            $table->string('telepon');
            $table->string('kota')->nullable();
            $table->text('alamat_lengkap')->nullable();

            // Metode
            $table->string('metode_pengiriman')->default('JNE Reguler');
            $table->string('metode_pembayaran')->default('QRIS');

            // Nominal
            $table->integer('subtotal');
            $table->integer('ongkir')->default(11500);
            $table->integer('diskon')->default(0);
            $table->integer('total');

            // Payment
            $table->timestamp('batas_bayar')->nullable(); // deadline pembayaran
            $table->timestamp('dibayar_pada')->nullable();

            $table->timestamps();
        });

        Schema::create('item_pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->foreignId('varian_id')->nullable()->constrained('varian_produks')->nullOnDelete();
            $table->string('nama_produk');
            $table->string('varian_label')->nullable();
            $table->integer('harga');
            $table->integer('jumlah');
            $table->string('gambar_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_pesanans');
        Schema::dropIfExists('pesanans');
    }
};
