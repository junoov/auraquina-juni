<?php

namespace App\Filament\Admin\Resources\Vouchers;

use App\Filament\Admin\Resources\Vouchers\Pages\CreateVoucher;
use App\Filament\Admin\Resources\Vouchers\Pages\EditVoucher;
use App\Filament\Admin\Resources\Vouchers\Pages\ListVouchers;
use App\Filament\Admin\Resources\Vouchers\Schemas\VoucherForm;
use App\Filament\Admin\Resources\Vouchers\Tables\VouchersTable;
use App\Models\Voucher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Voucher';

    protected static ?string $modelLabel = 'Voucher';

    protected static ?string $pluralModelLabel = 'Voucher';

    protected static string|\UnitEnum|null $navigationGroup = 'Promo';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return VoucherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VouchersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVouchers::route('/'),
            'create' => CreateVoucher::route('/create'),
            'edit' => EditVoucher::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool { return auth()->user()?->can('view_voucher') ?? false; }
    public static function canCreate(): bool { return auth()->user()?->can('create_voucher') ?? false; }
    public static function canEdit($record): bool { return auth()->user()?->can('update_voucher') ?? false; }
    public static function canDelete($record): bool { return auth()->user()?->can('delete_voucher') ?? false; }
    public static function canDeleteAny(): bool { return auth()->user()?->can('delete_voucher') ?? false; }
}
