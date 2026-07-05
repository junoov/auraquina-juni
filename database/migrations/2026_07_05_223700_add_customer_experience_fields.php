<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_addresses')) {
            Schema::create('user_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('label')->default('Alamat');
                $table->string('recipient_name');
                $table->string('phone', 30);
                $table->string('city');
                $table->text('address');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('user_addresses', function (Blueprint $table) {
                if (! Schema::hasColumn('user_addresses', 'user_id')) {
                    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                }
                if (! Schema::hasColumn('user_addresses', 'label')) {
                    $table->string('label')->default('Alamat');
                }
                if (! Schema::hasColumn('user_addresses', 'recipient_name')) {
                    $table->string('recipient_name');
                }
                if (! Schema::hasColumn('user_addresses', 'phone')) {
                    $table->string('phone', 30);
                }
                if (! Schema::hasColumn('user_addresses', 'city')) {
                    $table->string('city');
                }
                if (! Schema::hasColumn('user_addresses', 'address')) {
                    $table->text('address');
                }
                if (! Schema::hasColumn('user_addresses', 'is_default')) {
                    $table->boolean('is_default')->default(false);
                }
                if (! Schema::hasColumn('user_addresses', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (! Schema::hasColumn('user_addresses', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        Schema::table('pesanans', function (Blueprint $table) {
            if (! Schema::hasColumn('pesanans', 'after_sales_solution')) {
                $table->string('after_sales_solution')->nullable()->after('after_sales_type');
            }
            if (! Schema::hasColumn('pesanans', 'after_sales_items')) {
                $table->json('after_sales_items')->nullable()->after('after_sales_reason');
            }
            if (! Schema::hasColumn('pesanans', 'after_sales_evidence')) {
                $table->json('after_sales_evidence')->nullable()->after('after_sales_items');
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('reviews', 'photos')) {
                $table->json('photos')->nullable()->after('review');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'photos')) {
                $table->dropColumn('photos');
            }
        });

        Schema::table('pesanans', function (Blueprint $table) {
            $columns = collect(['after_sales_solution', 'after_sales_items', 'after_sales_evidence'])
                ->filter(fn (string $column) => Schema::hasColumn('pesanans', $column))
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::dropIfExists('user_addresses');
    }
};
