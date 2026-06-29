@php
    /**
     * Custom Infolist view: daftar item pesanan.
     * Tampilan card-based yang clean — gambar kecil + info inline.
     * Semua styling ada di theme.css (.order-item-* classes).
     *
     * State yang masuk = collection of ItemPesanan.
     */
    $items = $getState();
    $totalItems = 0;
    $grandTotal = 0;
@endphp

@if ($items && $items->isNotEmpty())
    <div class="order-items-wrapper">
        @foreach ($items as $item)
            @php
                $totalItems += $item->jumlah;
                $lineTotal = $item->harga * $item->jumlah;
                $grandTotal += $lineTotal;
            @endphp
            <div class="order-item-card">
                {{-- Gambar --}}
                <div class="order-item-thumb">
                    @if ($item->gambar_url)
                        <img src="{{ $item->full_gambar_url }}" alt="{{ $item->nama_produk }}" loading="lazy" />
                    @else
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                    @endif
                </div>

                {{-- Info produk --}}
                <div class="order-item-info">
                    <p class="order-item-name">{{ $item->nama_produk }}</p>
                    @if ($item->varian_label)
                        <p class="order-item-variant">{{ $item->varian_label }}</p>
                    @endif
                </div>

                {{-- Harga & qty --}}
                <div class="order-item-pricing">
                    <span class="order-item-price">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                    <span class="order-item-qty">× {{ $item->jumlah }}</span>
                </div>

                {{-- Subtotal --}}
                <div class="order-item-subtotal">
                    Rp {{ number_format($lineTotal, 0, ',', '.') }}
                </div>
            </div>
        @endforeach

        {{-- Footer --}}
        <div class="order-items-footer">
            <span class="order-items-footer-label">
                Total <strong>{{ $totalItems }}</strong> item
            </span>
            <span class="order-items-footer-total">
                Rp {{ number_format($grandTotal, 0, ',', '.') }}
            </span>
        </div>
    </div>
@else
    <p class="order-items-empty">Tidak ada item dalam pesanan ini.</p>
@endif
