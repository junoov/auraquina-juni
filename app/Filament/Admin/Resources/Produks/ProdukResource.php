<?php

namespace App\Filament\Admin\Resources\Produks;

use App\Filament\Admin\Resources\Produks\Pages\CreateProduk;
use App\Filament\Admin\Resources\Produks\Pages\EditProduk;
use App\Filament\Admin\Resources\Produks\Pages\ListProduks;
use App\Filament\Admin\Resources\Produks\RelationManagers\GambarsRelationManager;
use App\Filament\Admin\Resources\Produks\RelationManagers\VariansRelationManager;
use App\Filament\Admin\Resources\Produks\Schemas\ProdukForm;
use App\Filament\Admin\Resources\Produks\Tables\ProduksTable;
use App\Models\Produk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $modelLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk';

    protected static string|\UnitEnum|null $navigationGroup = 'Katalog';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return ProdukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            VariansRelationManager::class,
            GambarsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProduks::route('/'),
            'create' => CreateProduk::route('/create'),
            'edit' => EditProduk::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool { return auth()->user()?->can('view_produk') ?? false; }
    public static function canCreate(): bool { return auth()->user()?->can('create_produk') ?? false; }
    public static function canEdit($record): bool { return auth()->user()?->can('update_produk') ?? false; }
    public static function canDelete($record): bool { return auth()->user()?->can('delete_produk') ?? false; }
    public static function canDeleteAny(): bool { return auth()->user()?->can('delete_produk') ?? false; }
}
