<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->text('midtrans_snap_token')->nullable()->after('dibayar_pada');
            $table->text('midtrans_redirect_url')->nullable()->after('midtrans_snap_token');
            $table->string('midtrans_status')->nullable()->after('midtrans_redirect_url');
            $table->text('midtrans_raw_response')->nullable()->after('midtrans_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn([
                'midtrans_snap_token',
                'midtrans_redirect_url',
                'midtrans_status',
                'midtrans_raw_response',
            ]);
        });
    }
};
