@php
    /**
     * Custom Infolist view: timeline aktivitas pesanan.
     * Vertical timeline yang clean, event terbaru di atas.
     * Semua styling ada di theme.css (.activity-* classes).
     *
     * State yang masuk = collection of Activity.
     */
    $activities = $getState();
@endphp

@if ($activities && $activities->isNotEmpty())
    <div class="activity-timeline">

        @foreach ($activities as $index => $activity)
            @php
                $props = $activity->properties ?? collect();
                $from = $props->get('from_status');
                $to = $props->get('to_status');
                $actor = $props->get('actor');
                $causer = $activity->causer?->name ?? ($actor ? ucfirst($actor) : 'Sistem');
                $isFirst = $index === 0;
            @endphp

            <div class="activity-entry {{ $isFirst ? 'activity-entry--latest' : '' }}">
                {{-- Dot --}}
                <div class="activity-dot">
                    <span></span>
                </div>

                {{-- Content --}}
                <div class="activity-body">
                    <p class="activity-desc">
                        @if ($from && $to)
                            <span class="activity-desc-label">{{ ucfirst(str_replace('_', ' ', $from)) }}</span>
                            <span class="activity-desc-arrow">→</span>
                            <span class="activity-desc-label">{{ ucfirst(str_replace('_', ' ', $to)) }}</span>
                        @elseif ($props->get('field') === 'shipping_address')
                            Alamat pengiriman diubah
                        @else
                            {{ ucfirst(str_replace('_', ' ', (string) $activity->description)) }}
                        @endif
                    </p>
                    <p class="activity-meta">
                        <strong>{{ $causer }}</strong> · {{ optional($activity->created_at)->diffForHumans() }}
                        @if ($activity->created_at)
                            ({{ $activity->created_at->format('d M Y, H:i') }})
                        @endif
                    </p>

                    {{-- Catatan tambahan --}}
                    @if ($props->has('alasan'))
                        <div class="activity-reason">
                            Alasan: {{ $props->get('alasan') }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="activity-empty">Belum ada aktivitas tercatat untuk pesanan ini.</p>
@endif
