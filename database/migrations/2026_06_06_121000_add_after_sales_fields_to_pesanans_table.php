<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->string('after_sales_status')->nullable()->after('status');
            $table->string('after_sales_type')->nullable()->after('after_sales_status');
            $table->text('after_sales_reason')->nullable()->after('after_sales_type');
            $table->timestamp('after_sales_requested_at')->nullable()->after('after_sales_reason');
            $table->timestamp('after_sales_resolved_at')->nullable()->after('after_sales_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn([
                'after_sales_status',
                'after_sales_type',
                'after_sales_reason',
                'after_sales_requested_at',
                'after_sales_resolved_at',
            ]);
        });
    }
};
