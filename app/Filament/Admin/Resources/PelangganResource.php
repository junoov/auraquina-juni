<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Pelanggan\Pages\ListPelanggans;
use App\Filament\Admin\Resources\Pelanggan\Pages\ViewPelanggan;
use App\Filament\Admin\Resources\Pelanggan\Pages\EditPelanggan;
use App\Filament\Admin\Resources\Pelanggan\Schemas\PelangganInfolist;
use App\Filament\Admin\Resources\Pelanggan\Schemas\PelangganForm;
use App\Filament\Admin\Resources\Pelanggan\Tables\PelanggansTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PelangganResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Pelanggan';

    protected static ?string $modelLabel = 'Pelanggan';

    protected static ?string $pluralModelLabel = 'Pelanggan';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan Toko';

    protected static ?int $navigationSort = 85;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', fn ($query) => $query->where('name', 'pelanggan'));
    }

    public static function form(Schema $schema): Schema
    {
        return PelangganForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PelangganInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PelanggansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPelanggans::route('/'),
            'view' => ViewPelanggan::route('/{record}'),
            'edit' => EditPelanggan::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_customer') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update_customer') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete_customer') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('delete_customer') ?? false;
    }
}
