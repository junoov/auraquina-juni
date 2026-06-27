<?php

namespace App\Filament\Admin\Resources\Halamans;

use App\Filament\Admin\Resources\Halamans\Pages\CreateHalaman;
use App\Filament\Admin\Resources\Halamans\Pages\EditHalaman;
use App\Filament\Admin\Resources\Halamans\Pages\ListHalamans;
use App\Filament\Admin\Resources\Halamans\Schemas\HalamanForm;
use App\Filament\Admin\Resources\Halamans\Tables\HalamansTable;
use App\Models\Halaman;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HalamanResource extends Resource
{
    protected static ?string $model = Halaman::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Halaman';

    protected static ?string $modelLabel = 'Halaman';

    protected static ?string $pluralModelLabel = 'Halaman';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 70;

    protected static ?string $slug = 'halaman';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return HalamanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HalamansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHalamans::route('/'),
            'create' => CreateHalaman::route('/create'),
            'edit' => EditHalaman::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool { return auth()->user()?->can('view_halaman') ?? false; }
    public static function canCreate(): bool { return auth()->user()?->can('create_halaman') ?? false; }
    public static function canEdit($record): bool { return auth()->user()?->can('update_halaman') ?? false; }
    public static function canDelete($record): bool { return auth()->user()?->can('delete_halaman') ?? false; }
    public static function canDeleteAny(): bool { return auth()->user()?->can('delete_halaman') ?? false; }
}
