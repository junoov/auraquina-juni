<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gambar_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->string('url');
            $table->string('alt')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('utama')->default(false); // gambar utama
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gambar_produks');
    }
};
