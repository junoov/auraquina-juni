<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->string('kurir_pengiriman')->nullable()->after('metode_pengiriman');
            $table->string('nomor_resi')->nullable()->after('kurir_pengiriman');
            $table->timestamp('dikirim_pada')->nullable()->after('nomor_resi');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn([
                'kurir_pengiriman',
                'nomor_resi',
                'dikirim_pada',
            ]);
        });
    }
};
