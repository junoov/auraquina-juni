<?php

namespace App\Filament\Admin\Pages;

use App\Models\VarianProduk;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Facades\Activity;

class StokManagement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Stok';

    protected static ?string $title = 'Manajemen Stok';

    protected static string|\UnitEnum|null $navigationGroup = 'Katalog';

    protected static ?int $navigationSort = 25;

    protected string $view = 'filament.admin.pages.stok-management';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_stok') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->paginationMode(PaginationMode::Simple)
            ->query(
                VarianProduk::query()
                    ->with('produk')
            )
            ->columns([
                TextColumn::make('produk.nama')->label('Produk')->searchable()->wrap(),
                TextColumn::make('warna')->searchable(),
                TextColumn::make('ukuran'),
                TextColumn::make('sku')->label('SKU')->copyable(),
                TextColumn::make('stok')
                    ->numeric()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state <= 0 => 'danger',
                        $state < 5 => 'warning',
                        default => 'success',
                    })
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
                    ->label('Sesuaikan')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn () => auth()->user()?->can('adjust_stok'))
                    ->schema([
                        Select::make('mode')
                            ->label('Mode')
                            ->options([
                                'set' => 'Set Stok',
                                'add' => 'Tambah',
                                'sub' => 'Kurangi',
                            ])
                            ->default('add')
                            ->required(),
                        TextInput::make('jumlah')->numeric()->required()->minValue(1),
                        Textarea::make('alasan')->required(),
                    ])
                    ->action(function (VarianProduk $record, array $data) {
                        $before = $record->stok;
                        $record->stok = match ($data['mode']) {
                            'set' => (int) $data['jumlah'],
                            'add' => $before + (int) $data['jumlah'],
                            'sub' => max(0, $before - (int) $data['jumlah']),
                        };
                        $record->save();

                        activity('stok')
                            ->performedOn($record)
                            ->causedBy(auth()->user())
                            ->withProperties([
                                'mode' => $data['mode'],
                                'jumlah' => $data['jumlah'],
                                'before' => $before,
                                'after' => $record->stok,
                                'alasan' => $data['alasan'],
                            ])
                            ->log('stok_adjusted');

                        Notification::make()
                            ->title("Stok diperbarui {$before} → {$record->stok}")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
