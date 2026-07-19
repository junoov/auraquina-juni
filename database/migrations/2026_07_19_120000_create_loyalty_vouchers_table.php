<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('milestone');
            $table->string('code')->unique();
            $table->unsignedInteger('value')->default(15000);
            $table->timestamp('used_at')->nullable();
            $table->foreignId('pesanan_id')->nullable()->constrained('pesanans')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'milestone']);
        });

        Schema::table('pesanans', function (Blueprint $table) {
            $table->foreignId('loyalty_voucher_id')->nullable()->after('voucher_id')
                ->constrained('loyalty_vouchers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loyalty_voucher_id');
        });

        Schema::dropIfExists('loyalty_vouchers');
    }
};
