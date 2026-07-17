<?php

namespace App\Filament\Admin\Resources\Reviews;

use App\Filament\Admin\Resources\Reviews\Pages\EditReview;
use App\Filament\Admin\Resources\Reviews\Pages\ListReviews;
use App\Filament\Admin\Resources\Reviews\Schemas\ReviewForm;
use App\Filament\Admin\Resources\Reviews\Tables\ReviewsTable;
use App\Models\Review;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftEllipsis;

    protected static ?string $navigationLabel = 'Ulasan Pelanggan';

    protected static ?string $modelLabel = 'Ulasan';

    protected static ?string $pluralModelLabel = 'Ulasan';

    protected static string|\UnitEnum|null $navigationGroup = 'Produk & Stok';

    protected static ?int $navigationSort = 40;

    protected static ?string $recordTitleAttribute = 'review';

    public static function form(Schema $schema): Schema
    {
        return ReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReviewsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_review') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update_review') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete_review') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('delete_review') ?? false;
    }
}
