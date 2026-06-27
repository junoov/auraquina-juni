<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::updateOrCreate(
            ['code' => 'AURA10'],
            [
                'name' => 'Auraquina 10% Off',
                'type' => Voucher::TYPE_PERCENT,
                'value' => 10,
                'min_subtotal' => 250000,
                'max_discount' => 50000,
                'active' => true,
            ]
        );

        Voucher::updateOrCreate(
            ['code' => 'FREESHIP500'],
            [
                'name' => 'Gratis Ongkir Rp500K',
                'type' => Voucher::TYPE_FREE_SHIPPING,
                'value' => 0,
                'min_subtotal' => 500000,
                'active' => true,
            ]
        );
    }
}
