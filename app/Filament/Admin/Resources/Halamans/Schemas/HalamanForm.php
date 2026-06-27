<?php

namespace App\Filament\Admin\Resources\Halamans\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class HalamanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Halaman')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Judul')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                    TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true),
                    TextInput::make('eyebrow')->label('Label Kecil')->maxLength(255),
                    TextInput::make('urutan')->label('Urutan')->numeric()->default(0)->required(),
                    Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                    Toggle::make('aktif')->label('Aktif')->default(true),
                ]),
            Section::make('Isi Halaman')
                ->schema([
                    Repeater::make('sections')
                        ->label('Bagian Konten')
                        ->schema([
                            TextInput::make('heading')->label('Judul Bagian')->required(),
                            Repeater::make('body')
                                ->label('Paragraf')
                                ->simple(
                                    Textarea::make('isi')->label('Teks')->rows(3)->required()
                                )
                                ->formatStateUsing(fn ($state) => collect($state ?? [])->map(fn ($item) => is_array($item) ? ($item['isi'] ?? '') : $item)->all())
                                ->dehydrateStateUsing(fn ($state) => collect($state ?? [])->map(fn ($item) => is_array($item) ? ($item['isi'] ?? '') : $item)->filter()->values()->all())
                                ->defaultItems(1),
                        ])
                        ->defaultItems(1)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
