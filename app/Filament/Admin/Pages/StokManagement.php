<?php

namespace App\Filament\Admin\Pages;

use App\Models\VarianProduk;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class StokManagement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Stok Produk';

    protected static ?string $title = 'Stok Produk';

    protected static string|\UnitEnum|null $navigationGroup = 'Produk & Stok';

    protected static ?int $navigationSort = 25;

    protected string $view = 'filament.admin.pages.stok-management';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_stok') ?? false;
    }

    /**
     * Stat cards — dihitung sekali per request.
     */
    #[Computed]
    public function stokStats(): array
    {
        return (array) DB::table('varian_produks')->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN stok >= 5 THEN 1 ELSE 0 END) as aman,
            SUM(CASE WHEN stok > 0 AND stok < 5 THEN 1 ELSE 0 END) as rendah,
            SUM(CASE WHEN stok <= 0 THEN 1 ELSE 0 END) as habis
        ')->first() ?: [
            'total' => 0,
            'aman' => 0,
            'rendah' => 0,
            'habis' => 0,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->searchPlaceholder('Cari produk')
            ->paginationMode(PaginationMode::Simple)
            ->query(
                VarianProduk::query()
                    ->with('produk')
            )
            ->columns([
                TextColumn::make('produk.nama')
                    ->label('Produk')
                    ->searchable()
                    ->wrap()
                    ->weight(FontWeight::SemiBold),
                TextColumn::make('varian_label')
                    ->label('Varian')
                    ->state(fn ($record): string => trim("{$record->warna} / {$record->ukuran}"))
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->visibleFrom('sm'),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->copyable()
                    ->tooltip('Klik untuk menyalin kode')
                    ->color('gray')
                    ->visibleFrom('md'),
                TextColumn::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 0 => 'danger',
                        $state < 5 => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn ($state) => $state <= 0
                        ? 'Stok habis'
                        : ($state < 5 ? "Sisa {$state} unit" : "Stok aman: {$state} unit"))
                    ->sortable(),
            ])
            ->defaultSort('stok')
            ->filters([
                Filter::make('low_stock')
                    ->label('Stok Rendah (<5)')
                    ->query(fn (Builder $q) => $q->where('stok', '<', 5))
                    ->toggle(),
                Filter::make('out_of_stock')
                    ->label('Habis')
                    ->query(fn (Builder $q) => $q->where('stok', 0))
                    ->toggle(),
                SelectFilter::make('produk_id')
                    ->relationship('produk', 'nama')
                    ->label('Produk')
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('adjust')
                    ->label('Ubah stok')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->slideOver()
                    ->modalSubmitActionLabel('Simpan stok')
                    ->extraAttributes([
                        'aria-label' => 'Ubah stok',
                        'class' => 'stock-adjust-action',
                    ])
                    ->visible(fn () => auth()->user()?->can('adjust_stok'))
                    ->schema(fn (VarianProduk $record): array => [
                        ViewField::make('stok_preview')
                            ->view('filament.admin.components.stok-preview-box')
                            ->viewData([
                                'stok' => $record->stok,
                            ])
                            ->columnSpanFull()
                            ->dehydrated(false),

                        Radio::make('mode')
                            ->label('Cara mengubah stok')
                            ->options([
                                'add' => 'Tambah stok',
                                'sub' => 'Kurangi stok',
                                'set' => 'Ganti angka stok',
                            ])
                            ->default('add')
                            ->required()
                            ->inline()
                            ->columnSpanFull()
                            ->descriptions([
                                'add' => 'Tambahkan barang baru yang masuk',
                                'sub' => 'Kurangi barang yang rusak/hilang',
                                'set' => 'Timpa stok dengan angka saat ini',
                            ]),

                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(1)
                            ->live(debounce: '300ms')
                            ->columnSpanFull()
                            ->helperText(fn ($state, $get) => self::previewStok($record->stok, $get('mode'), (int) $state)),
                    ])
                    ->modalFooterActionsAlignment('start')
                    ->action(function (VarianProduk $record, array $data) {
                        $before = $record->stok;
                        $record->stok = match ($data['mode']) {
                            'set' => (int) $data['jumlah'],
                            'add' => $before + (int) $data['jumlah'],
                            'sub' => max(0, $before - (int) $data['jumlah']),
                        };
                        $record->save();

                        Notification::make()
                            ->title("Stok diperbarui: {$before} → {$record->stok}")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    /**
     * Preview stok hasil perubahan untuk helper text.
     */
    private static function previewStok(int $current, ?string $mode, int $jumlah): string
    {
        if (! $mode || $jumlah <= 0 && $mode !== 'set') {
            return 'Masukkan jumlah yang ingin diubah.';
        }

        $result = match ($mode) {
            'set' => $jumlah,
            'add' => $current + $jumlah,
            'sub' => max(0, $current - $jumlah),
            default => $current,
        };

        $diff = $result - $current;
        $sign = $diff >= 0 ? '+' : '';

        return "Hasil akhir: {$current} menjadi {$result} ({$sign}{$diff}).";
    }
}
