@php
    /**
     * Stok produk — ringkasan di atas tabel.
     * Stat cards dihitung via Livewire #[Computed] property.
     */
    $stats = $this->stokStats;
@endphp

<x-filament-panels::page>

    <div class="stok-stats-grid">
        <div class="stok-stat-card stok-stat-card--total">
            <div class="stok-stat-icon" aria-hidden="true">@svg('heroicon-o-archive-box', 'stok-stat-icon-svg')</div>
            <div class="stok-stat-body">
                <span class="stok-stat-value">{{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</span>
                <span class="stok-stat-label">Total Varian</span>
            </div>
        </div>

        <div class="stok-stat-card stok-stat-card--safe">
            <div class="stok-stat-icon" aria-hidden="true">@svg('heroicon-o-check-circle', 'stok-stat-icon-svg')</div>
            <div class="stok-stat-body">
                <span class="stok-stat-value">{{ number_format($stats['aman'] ?? 0, 0, ',', '.') }}</span>
                <span class="stok-stat-label">Aman</span>
            </div>
        </div>

        <div class="stok-stat-card stok-stat-card--low">
            <div class="stok-stat-icon" aria-hidden="true">@svg('heroicon-o-exclamation-triangle', 'stok-stat-icon-svg')</div>
            <div class="stok-stat-body">
                <span class="stok-stat-value">{{ number_format($stats['rendah'] ?? 0, 0, ',', '.') }}</span>
                <span class="stok-stat-label">Hampir habis</span>
            </div>
        </div>

        <div class="stok-stat-card stok-stat-card--empty">
            <div class="stok-stat-icon" aria-hidden="true">@svg('heroicon-o-x-circle', 'stok-stat-icon-svg')</div>
            <div class="stok-stat-body">
                <span class="stok-stat-value">{{ number_format($stats['habis'] ?? 0, 0, ',', '.') }}</span>
                <span class="stok-stat-label">Habis</span>
            </div>
        </div>
    </div>

    {{ $this->table }}

</x-filament-panels::page>
