<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('review');
            $table->string('status')->default('approved');
            $table->timestamps();

            $table->unique(['user_id', 'produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
