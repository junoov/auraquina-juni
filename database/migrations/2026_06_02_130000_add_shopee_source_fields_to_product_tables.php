<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->unsignedBigInteger('shopee_item_id')->nullable()->unique()->after('urutan');
            $table->unsignedBigInteger('shopee_shop_id')->nullable()->after('shopee_item_id');
            $table->string('shopee_url')->nullable()->after('shopee_shop_id');
            $table->decimal('rating_star', 4, 2)->nullable()->after('shopee_url');
            $table->string('stock_display')->nullable()->after('rating_star');
            $table->json('source_categories')->nullable()->after('stock_display');
        });

        Schema::table('varian_produks', function (Blueprint $table) {
            $table->unsignedBigInteger('shopee_model_id')->nullable()->unique()->after('penyesuaian_harga');
        });

        Schema::table('gambar_produks', function (Blueprint $table) {
            $table->string('shopee_image_id')->nullable()->after('utama');
        });

        Schema::table('gambar_varian_produks', function (Blueprint $table) {
            $table->string('shopee_image_id')->nullable()->after('utama');
        });
    }

    public function down(): void
    {
        Schema::table('gambar_varian_produks', function (Blueprint $table) {
            $table->dropColumn('shopee_image_id');
        });

        Schema::table('gambar_produks', function (Blueprint $table) {
            $table->dropColumn('shopee_image_id');
        });

        Schema::table('varian_produks', function (Blueprint $table) {
            $table->dropUnique(['shopee_model_id']);
            $table->dropColumn('shopee_model_id');
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->dropUnique(['shopee_item_id']);
            $table->dropColumn([
                'shopee_item_id',
                'shopee_shop_id',
                'shopee_url',
                'rating_star',
                'stock_display',
                'source_categories',
            ]);
        });
    }
};
