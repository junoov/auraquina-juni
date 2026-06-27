<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_keranjangs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index(); // untuk guest cart
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->foreignId('varian_id')->nullable()->constrained('varian_produks')->nullOnDelete();
            $table->integer('jumlah')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_keranjangs');
    }
};
