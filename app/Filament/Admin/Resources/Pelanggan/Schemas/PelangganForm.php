<?php

namespace App\Filament\Admin\Resources\Pelanggan\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class PelangganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nama')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('phone')
                ->label('Telepon')
                ->tel()
                ->maxLength(20)
                ->placeholder('—'),
            TextInput::make('password')
                ->label('Kata Sandi Baru')
                ->password()
                ->revealable()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->placeholder('Kosongkan jika tidak ingin mengubah kata sandi')
                ->minLength(8),
        ]);
    }
}
