@php
    /**
     * Stok Management — View dengan stat cards di atas tabel.
     * Stat cards dihitung via Livewire #[Computed] property.
     */
    $stats = $this->stokStats;
@endphp

<x-filament-panels::page>

    {{-- Stat Cards --}}
    <div class="stok-stats-grid">
        {{-- Total Varian --}}
        <div class="stok-stat-card stok-stat-card--total">
            <div class="stok-stat-icon">📦</div>
            <div class="stok-stat-body">
                <span class="stok-stat-value">{{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</span>
                <span class="stok-stat-label">Total Varian</span>
            </div>
        </div>

        {{-- Stok Aman --}}
        <div class="stok-stat-card stok-stat-card--safe">
            <div class="stok-stat-icon">✅</div>
            <div class="stok-stat-body">
                <span class="stok-stat-value">{{ number_format($stats['aman'] ?? 0, 0, ',', '.') }}</span>
                <span class="stok-stat-label">Stok Aman (≥5)</span>
            </div>
        </div>

        {{-- Stok Rendah --}}
        <div class="stok-stat-card stok-stat-card--low">
            <div class="stok-stat-icon">⚠️</div>
            <div class="stok-stat-body">
                <span class="stok-stat-value">{{ number_format($stats['rendah'] ?? 0, 0, ',', '.') }}</span>
                <span class="stok-stat-label">Stok Rendah (<5)</span>
            </div>
        </div>

        {{-- Stok Habis --}}
        <div class="stok-stat-card stok-stat-card--empty">
            <div class="stok-stat-icon">🚫</div>
            <div class="stok-stat-body">
                <span class="stok-stat-value">{{ number_format($stats['habis'] ?? 0, 0, ',', '.') }}</span>
                <span class="stok-stat-label">Stok Habis</span>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    {{ $this->table }}

</x-filament-panels::page>
