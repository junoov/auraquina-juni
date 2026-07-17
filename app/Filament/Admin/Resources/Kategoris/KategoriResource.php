<?php

namespace App\Filament\Admin\Resources\Kategoris;

use App\Filament\Admin\Resources\Kategoris\Pages\CreateKategori;
use App\Filament\Admin\Resources\Kategoris\Pages\EditKategori;
use App\Filament\Admin\Resources\Kategoris\Pages\ListKategoris;
use App\Filament\Admin\Resources\Kategoris\Schemas\KategoriForm;
use App\Filament\Admin\Resources\Kategoris\Tables\KategorisTable;
use App\Models\Kategori;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Kategori Produk';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori';

    protected static string|\UnitEnum|null $navigationGroup = 'Produk & Stok';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return KategoriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KategorisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKategoris::route('/'),
            'create' => CreateKategori::route('/create'),
            'edit' => EditKategori::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_kategori') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_kategori') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update_kategori') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete_kategori') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('delete_kategori') ?? false;
    }
}
