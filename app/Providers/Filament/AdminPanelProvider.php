<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\StokManagement;
use App\Filament\Admin\Resources\Halamans\HalamanResource;
use App\Filament\Admin\Resources\Kategoris\KategoriResource;
use App\Filament\Admin\Resources\Pesanans\PesananResource;
use App\Filament\Admin\Resources\Produks\ProdukResource;
use App\Filament\Admin\Resources\Reviews\ReviewResource;
use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Admin\Resources\Vouchers\VoucherResource;
use App\Filament\Admin\Widgets\DashboardStats;
use App\Filament\Admin\Widgets\RecentOrders;
use App\Filament\Admin\Widgets\StatusPesananChart;
use App\Http\Middleware\SetAdminLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->brandName('Admin Auraquina')
            ->spa()
            ->globalSearchDebounce('750ms')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                KategoriResource::class,
                ProdukResource::class,
                ReviewResource::class,
                PesananResource::class,
                VoucherResource::class,
                HalamanResource::class,
                UserResource::class,
                RoleResource::class,
            ])
            ->pages([
                Dashboard::class,
                StokManagement::class,
            ])
            ->widgets([
                DashboardStats::class,
                StatusPesananChart::class,
                RecentOrders::class,
            ])
            ->navigationGroups([
                'Katalog',
                'Pesanan',
                'Promo',
                'Pengaturan',
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                SetAdminLocale::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
