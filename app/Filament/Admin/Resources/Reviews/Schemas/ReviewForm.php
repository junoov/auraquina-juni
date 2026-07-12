<?php

namespace App\Filament\Admin\Resources\Reviews\Schemas;

use App\Models\Review;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Ulasan')
                ->columns(2)
                ->schema([
                    TextInput::make('produk_nama')
                        ->label('Produk')
                        ->formatStateUsing(fn ($record) => $record?->produk?->nama)
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('user_name')
                        ->label('Pelanggan')
                        ->formatStateUsing(fn ($record) => $record?->user?->name)
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('rating')->label('Rating')->disabled()->dehydrated(false),
                    Select::make('status')
                        ->label('Status')
                        ->options(Review::statusOptions())
                        ->required(),
                    Textarea::make('review')
                        ->label('Isi Ulasan')
                        ->rows(8)
                        ->columnSpanFull()
                        ->disabled()
                        ->dehydrated(false),
                ]),
        ]);
    }
}
