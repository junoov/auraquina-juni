<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'rizky@example.com'],
            [
                'name' => 'Rizky Noviansyah',
                'phone' => '0811-3662-636',
                'password' => Hash::make('password'),
            ]
        );
    }
}
