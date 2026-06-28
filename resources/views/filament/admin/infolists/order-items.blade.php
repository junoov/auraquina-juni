@php
    /**
     * Custom Infolist view: daftar item pesanan.
     * Tampilan seperti keranjang belanja (gambar + nama + varian + qty + harga),
     * BUKAN tabel kering — supaya admin pemula gampang dibaca.
     *
     * State yang masuk = collection of ItemPesanan.
     */
    $items = $getState();
    $totalItems = 0;
    $grandTotal = 0;
@endphp

@if ($items && $items->isNotEmpty())
    <div class="space-y-3">
        @foreach ($items as $item)
            @php
                $totalItems += $item->jumlah;
                $lineTotal = $item->harga * $item->jumlah;
                $grandTotal += $lineTotal;
            @endphp
            <div class="flex items-start gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                {{-- Gambar --}}
                <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800">
                    @if ($item->gambar_url)
                        <img src="{{ $item->full_gambar_url }}" alt="{{ $item->nama_produk }}" class="h-full w-full object-cover" loading="lazy" />
                    @else
                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->nama_produk }}</p>
                    @if ($item->varian_label)
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $item->varian_label }}</p>
                    @endif
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Rp {{ number_format($item->harga, 0, ',', '.') }} × {{ $item->jumlah }}
                    </p>
                </div>

                {{-- Subtotal baris --}}
                <div class="flex-shrink-0 text-right">
                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($lineTotal, 0, ',', '.') }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Ringkasan bawah --}}
    <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-3 dark:border-gray-700">
        <span class="text-sm text-gray-500 dark:text-gray-400">Total {{ $totalItems }} item</span>
        <span class="text-base font-bold text-primary-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </div>
@else
    <p class="text-sm text-gray-400 italic">Tidak ada item dalam pesanan ini.</p>
@endif
