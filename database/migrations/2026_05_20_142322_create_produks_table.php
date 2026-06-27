<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoris')->cascadeOnDelete();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->text('deskripsi_singkat')->nullable();
            $table->integer('harga'); // dalam rupiah
            $table->integer('harga_coret')->nullable(); // harga sebelum diskon
            $table->string('sku')->unique();
            $table->integer('berat')->default(0); // dalam gram
            $table->string('bahan')->nullable();
            $table->text('perawatan')->nullable();
            $table->text('info_model')->nullable();
            $table->boolean('aktif')->default(true);
            $table->boolean('unggulan')->default(false); // tampil di homepage
            $table->enum('badge', ['baru', 'terlaris', 'terbatas', 'preorder'])->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
