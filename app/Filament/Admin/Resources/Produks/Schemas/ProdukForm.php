<?php

namespace App\Filament\Admin\Resources\Produks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Produk Tabs')
                    ->tabs([
                        // ─────────────────────────────────────────────
                        // TAB 1: Informasi Produk
                        // ─────────────────────────────────────────────
                        Tab::make('Informasi Produk')
                            ->icon(Heroicon::OutlinedInformationCircle)
                            ->schema([
                                Section::make('Nama & Kategori')
                                    ->description('Informasi dasar tentang produk.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('nama')
                                            ->label('Nama Produk')
                                            ->placeholder('Contoh: Gamis Auraquina Premium')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state)))
                                            ->columnSpanFull(),
                                        TextInput::make('slug')
                                            ->label('Link Produk')
                                            ->helperText('Otomatis dibuat dari nama produk. Bisa diedit manual jika perlu.')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->columnSpanFull(),
                                        Select::make('kategori_id')
                                            ->label('Kategori')
                                            ->relationship('kategori', 'nama')
                                            ->preload()
                                            ->searchable()
                                            ->required(),
                                        TextInput::make('sku')
                                            ->label('Kode Produk (SKU)')
                                            ->helperText('Kode unik untuk melacak stok produk ini.')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                    ]),

                                Section::make('Deskripsi')
                                    ->columns(1)
                                    ->schema([
                                        Textarea::make('deskripsi_singkat')
                                            ->label('Deskripsi Singkat')
                                            ->helperText('Ringkasan singkat produk yang muncul di halaman katalog. Maks 500 karakter.')
                                            ->maxLength(500)
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Textarea::make('deskripsi')
                                            ->label('Deskripsi Lengkap')
                                            ->helperText('Deskripsi detail produk yang muncul di halaman produk.')
                                            ->rows(6)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ─────────────────────────────────────────────
                        // TAB 2: Foto Produk
                        // ─────────────────────────────────────────────
                        Tab::make('Foto Produk')
                            ->icon(Heroicon::OutlinedPhoto)
                            ->schema([
                                Section::make('Upload Foto')
                                    ->description('Foto utama produk yang akan tampil di halaman detail toko.')
                                    ->schema([
                                        Repeater::make('gambars')
                                            ->relationship()
                                            ->hiddenLabel()
                                            ->addActionLabel('Tambah Foto Produk')
                                            ->reorderableWithButtons()
                                            ->grid([
                                                'default' => 1,
                                                'sm' => 2,
                                                'xl' => 3,
                                            ])
                                            ->schema([
                                                FileUpload::make('url')
                                                    ->hiddenLabel()
                                                    ->image()
                                                    ->disk('r2')
                                                    ->directory('produk')
                                                    ->imageEditor()
                                                    ->maxSize(5120)
                                                    ->required()
                                                    ->columnSpanFull(),
                                                TextInput::make('alt')
                                                    ->label('Keterangan Foto')
                                                    ->placeholder('Contoh: Gamis Auraquina Tampak Depan')
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),
                                                Toggle::make('utama')
                                                    ->label('Jadikan Foto Cover')
                                                    ->helperText('Foto ini muncul pertama di katalog')
                                                    ->default(false)
                                                    ->inline(false),
                                                TextInput::make('urutan')
                                                    ->label('Urutan Tampil')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->required(),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => $state['utama'] ? '🌟 Foto Cover' : '📷 Foto Detail')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ─────────────────────────────────────────────
                        // TAB 3: Harga & Varian
                        // ─────────────────────────────────────────────
                        Tab::make('Harga & Varian')
                            ->icon(Heroicon::OutlinedCurrencyDollar)
                            ->schema([
                                Section::make('Harga & Berat')
                                    ->description('Atur harga jual dan berat produk.')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('harga')
                                            ->label('Harga Jual')
                                            ->helperText('Harga yang dibayar pembeli.')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rp'),
                                        TextInput::make('harga_coret')
                                            ->label('Harga Sebelum Diskon')
                                            ->helperText('Harga lama sebelum potongan harga. Akan tampil sebagai harga dicoret (~~Rp 200.000~~ Rp 150.000).')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->nullable(),
                                        TextInput::make('berat')
                                            ->label('Berat (gram)')
                                            ->helperText('Untuk hitung ongkos kirim.')
                                            ->required()
                                            ->numeric()
                                            ->suffix('gram')
                                            ->default(0),
                                    ]),
                            ]),

                        // ─────────────────────────────────────────────
                        // TAB 4: Detail & Status
                        // ─────────────────────────────────────────────
                        Tab::make('Detail & Status')
                            ->icon(Heroicon::OutlinedCog6Tooth)
                            ->schema([
                                Section::make('Detail Produk')
                                    ->description('Informasi tambahan seperti bahan, perawatan, dan info model.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('bahan')
                                            ->label('Bahan')
                                            ->helperText('Contoh: Cotton Combed 30s, Sutra, Rayon')
                                            ->placeholder('Tulis bahan produk di sini'),
                                        Select::make('badge')
                                            ->label('Label / Tag Produk')
                                            ->helperText('Tanda khusus yang tampil di gambar produk, misalnya "Baru" atau "Terlaris".')
                                            ->options([
                                                'baru' => '✨ Baru',
                                                'terlaris' => '🔥 Terlaris',
                                                'terbatas' => '⚡ Terbatas',
                                                'preorder' => '📦 Pra-pesan',
                                            ])
                                            ->nullable(),
                                        Textarea::make('perawatan')
                                            ->label('Petunjuk Perawatan')
                                            ->placeholder('Contoh: Cuci dengan tangan, jangan gunakan pemutih.')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Textarea::make('info_model')
                                            ->label('Info Model')
                                            ->placeholder('Contoh: Tinggi 170cm, BB 55kg, pakai All Size')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Status & Tampilan')
                                    ->description('Atur apakah produk bisa dilihat pembeli dan posisinya di katalog.')
                                    ->columns(3)
                                    ->schema([
                                        Toggle::make('aktif')
                                            ->label('Aktif')
                                            ->helperText('Produk yang nonaktif tidak muncul di toko.')
                                            ->default(true)
                                            ->required(),
                                        Toggle::make('unggulan')
                                            ->label('Tampil di Halaman Utama')
                                            ->helperText('Jika aktif, produk akan ditampilkan di bagian rekomendasi/unggulan.'),
                                        TextInput::make('urutan')
                                            ->label('Urutan Tampil')
                                            ->helperText('Angka kecil = tampil lebih dulu di atas. Contoh: 1 tampil di atas angka 5.')
                                            ->numeric()
                                            ->default(0)
                                            ->required(),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}
