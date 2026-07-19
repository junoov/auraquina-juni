<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->json('voucher_codes')->nullable()->after('diskon');
        });

        DB::table('pesanans')
            ->whereNotNull('voucher_code')
            ->update([
                'voucher_codes' => DB::raw('JSON_ARRAY(voucher_code)'),
            ]);

        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loyalty_voucher_id');
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn('voucher_code');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('diskon')->constrained('vouchers')->nullOnDelete();
            $table->string('voucher_code')->nullable()->after('voucher_id');
            $table->foreignId('loyalty_voucher_id')->nullable()->after('voucher_code')
                ->constrained('loyalty_vouchers')->nullOnDelete();
        });

        DB::table('pesanans')
            ->whereNotNull('voucher_codes')
            ->update([
                'voucher_code' => DB::raw("JSON_UNQUOTE(JSON_EXTRACT(voucher_codes, '$[0]'))"),
            ]);

        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn('voucher_codes');
        });
    }
};
