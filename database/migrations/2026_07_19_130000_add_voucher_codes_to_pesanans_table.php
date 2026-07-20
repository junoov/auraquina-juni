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
            ->select(['id', 'voucher_code', 'loyalty_voucher_id'])
            ->where(fn ($query) => $query
                ->whereNotNull('voucher_code')
                ->orWhereNotNull('loyalty_voucher_id'))
            ->orderBy('id')
            ->chunkById(100, function ($pesanans): void {
                $loyaltyCodes = DB::table('loyalty_vouchers')
                    ->whereIn('id', $pesanans->pluck('loyalty_voucher_id')->filter())
                    ->pluck('code', 'id');

                foreach ($pesanans as $pesanan) {
                    $codes = array_values(array_unique(array_filter([
                        $pesanan->voucher_code,
                        $loyaltyCodes[$pesanan->loyalty_voucher_id] ?? null,
                    ])));

                    DB::table('pesanans')->where('id', $pesanan->id)->update([
                        'voucher_codes' => json_encode($codes, JSON_THROW_ON_ERROR),
                    ]);
                }
            });

        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loyalty_voucher_id');
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn('voucher_code');
        });
    }

    public function down(): void
    {
        DB::table('pesanans')
            ->select(['id', 'voucher_codes'])
            ->whereNotNull('voucher_codes')
            ->orderBy('id')
            ->chunkById(100, function ($pesanans): void {
                foreach ($pesanans as $pesanan) {
                    $codes = json_decode($pesanan->voucher_codes, true, flags: JSON_THROW_ON_ERROR);
                    $adminCount = DB::table('vouchers')->whereIn('code', $codes)->count();
                    $loyaltyCount = DB::table('loyalty_vouchers')->whereIn('code', $codes)->count();

                    if ($adminCount > 1 || $loyaltyCount > 1 || $adminCount + $loyaltyCount !== count($codes)) {
                        throw new RuntimeException("Pesanan {$pesanan->id} memakai voucher stacking yang tidak dapat disimpan oleh skema lama.");
                    }
                }
            });

        Schema::table('pesanans', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('diskon')->constrained('vouchers')->nullOnDelete();
            $table->string('voucher_code')->nullable()->after('voucher_id');
            $table->foreignId('loyalty_voucher_id')->nullable()->after('voucher_code')
                ->constrained('loyalty_vouchers')->nullOnDelete();
        });

        DB::table('pesanans')
            ->select(['id', 'voucher_codes'])
            ->whereNotNull('voucher_codes')
            ->orderBy('id')
            ->chunkById(100, function ($pesanans): void {
                foreach ($pesanans as $pesanan) {
                    $codes = json_decode($pesanan->voucher_codes, true, flags: JSON_THROW_ON_ERROR);
                    $adminVoucher = DB::table('vouchers')->whereIn('code', $codes)->first();
                    $loyaltyVoucher = DB::table('loyalty_vouchers')->whereIn('code', $codes)->first();

                    DB::table('pesanans')->where('id', $pesanan->id)->update([
                        'voucher_id' => $adminVoucher?->id,
                        'voucher_code' => $adminVoucher?->code ?? $loyaltyVoucher?->code,
                        'loyalty_voucher_id' => $loyaltyVoucher?->id,
                    ]);
                }
            });

        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn('voucher_codes');
        });
    }
};
