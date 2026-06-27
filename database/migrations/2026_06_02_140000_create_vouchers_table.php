<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type');
            $table->integer('value')->default(0);
            $table->integer('min_subtotal')->default(0);
            $table->integer('max_discount')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('pesanans', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('diskon')->constrained('vouchers')->nullOnDelete();
            $table->string('voucher_code')->nullable()->after('voucher_id');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn('voucher_code');
        });

        Schema::dropIfExists('vouchers');
    }
};
