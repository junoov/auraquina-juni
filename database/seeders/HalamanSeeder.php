<?php

namespace Database\Seeders;

use App\Models\Halaman;
use App\Support\HalamanDefaults;
use Illuminate\Database\Seeder;

class HalamanSeeder extends Seeder
{
    public function run(): void
    {
        Halaman::where('slug', 'size-guide')->delete();

        foreach (HalamanDefaults::items() as $index => $item) {
            Halaman::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'eyebrow' => $item['eyebrow'],
                    'description' => $item['description'],
                    'sections' => $item['sections'],
                    'aktif' => true,
                    'urutan' => $index,
                ]
            );
        }
    }
}
