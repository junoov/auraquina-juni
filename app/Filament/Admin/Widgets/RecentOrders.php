<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Pesanans\PesananResource;
use App\Filament\Admin\Resources\Pesanans\Schemas\PesananForm;
use App\Models\Pesanan;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrders extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Pesanan terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(Pesanan::query()->with('items')->latest()->limit(8))
            ->searchPlaceholder('Cari pesanan')
            ->emptyStateHeading('Belum ada pesanan')
            ->emptyStateDescription('Pesanan baru akan muncul di sini setelah pelanggan checkout.')
            ->columns([
                TextColumn::make('kode_pesanan')->label('Kode pesanan')->searchable()->copyable(),
                TextColumn::make('nama_penerima')->label('Penerima')->searchable()->wrap()->visibleFrom('sm'),
                TextColumn::make('total')->label('Total')->money('IDR', divideBy: 1)->visibleFrom('md'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => PesananForm::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'pending_payment' => 'warning',
                        'paid' => 'info',
                        'processing', 'packed', 'shipped' => 'primary',
                        'delivered', 'completed' => 'success',
                        'cancelled', 'expired' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Masuk')->since()->visibleFrom('lg'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Buka pesanan')
                    ->url(fn ($record) => PesananResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye')
                    ->extraAttributes([
                        'aria-label' => 'Buka pesanan',
                        'class' => 'recent-order-open-action',
                    ]),
            ])
            ->paginated(false);
    }
}
