<?php

namespace App\Filament\Admin\Resources\Produks\Pages;

use App\Filament\Admin\Resources\Produks\ProdukResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Enums\ContentTabPosition;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

class EditProduk extends EditRecord
{
    protected static string $resource = ProdukResource::class;

    /**
     * Gabungkan form produk dengan relation manager (varian) menjadi tabs sejajar,
     * mirip dengan layout admin panel Shopee Seller Center.
     */
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Detail Produk';
    }

    public function getContentTabIcon(): string | \Filament\Support\Icons\Heroicon | \Illuminate\Contracts\Support\Htmlable | null
    {
        return Heroicon::OutlinedClipboardDocumentList;
    }

    /**
     * Posisi tab "Detail Produk" diletakkan paling depan (sebelum tab Varian).
     */
    public function getContentTabPosition(): ?ContentTabPosition
    {
        return ContentTabPosition::Before;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
