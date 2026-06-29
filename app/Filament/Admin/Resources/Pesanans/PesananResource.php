<?php

namespace App\Filament\Admin\Resources\Pesanans;

use App\Filament\Admin\Resources\Pesanans\Pages\EditPesanan;
use App\Filament\Admin\Resources\Pesanans\Pages\ListPesanans;
use App\Filament\Admin\Resources\Pesanans\Pages\ViewPesanan;
use App\Filament\Admin\Resources\Pesanans\Schemas\PesananForm;
use App\Filament\Admin\Resources\Pesanans\Schemas\PesananInfolist;
use App\Filament\Admin\Resources\Pesanans\Tables\PesanansTable;
use App\Models\Pesanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Pesanan';

    protected static ?string $modelLabel = 'Pesanan';

    protected static ?string $pluralModelLabel = 'Pesanan';

    protected static string|\UnitEnum|null $navigationGroup = 'Pesanan';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'kode_pesanan';

    public static function form(Schema $schema): Schema
    {
        return PesananForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PesananInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PesanansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // ItemsRelationManager dihapus dari sini — items sudah dirender
            // sebagai custom blade view di PesananInfolist (Baris 3: Item Pesanan).
            // Relation manager menyebabkan duplikat section "Item Pesanan" di halaman View.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPesanans::route('/'),
            'view' => ViewPesanan::route('/{record}'),
            'edit' => EditPesanan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Cache::remember('admin.nav.pending_payment_count', 60, fn () => static::getModel()::where('status', 'pending_payment')->count());
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canViewAny(): bool { return auth()->user()?->can('view_pesanan') ?? false; }
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return auth()->user()?->can('update_pesanan') ?? false; }
    public static function canDelete($record): bool { return auth()->user()?->can('delete_pesanan') ?? false; }
    public static function canDeleteAny(): bool { return auth()->user()?->can('delete_pesanan') ?? false; }
}
