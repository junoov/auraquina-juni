@php
    /**
     * Custom Infolist view: timeline aktivitas pesanan.
     * Membaca dari spatie/activitylog yang SUDAH ditulis oleh Pesanan::transitionTo().
     *
     * Tampilan: vertical timeline, event terbaru di atas.
     * Tujuan: admin (terutama pemula) bisa lihat "siapa ngapain kapan"
     * untuk audit, dispute, atau troubleshooting.
     *
     * State yang masuk = collection of Activity.
     */
    $activities = $getState();
@endphp

@if ($activities && $activities->isNotEmpty())
    <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-3 space-y-4">
        @foreach ($activities as $activity)
            @php
                $props = $activity->properties ?? collect();
                $from = $props->get('from_status');
                $to = $props->get('to_status');
                $actor = $props->get('actor');
                $causer = $activity->causer?->name ?? ($actor ? ucfirst($actor) : 'Sistem');
            @endphp
            <li class="ml-4">
                <span class="absolute -left-[9px] mt-1 flex h-4 w-4 items-center justify-center rounded-full ring-4 ring-white bg-primary-500 dark:ring-gray-900"></span>
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        @if ($from && $to)
                            Status: {{ ucfirst(str_replace('_', ' ', $from)) }} → {{ ucfirst(str_replace('_', ' ', $to)) }}
                        @elseif ($props->get('field') === 'shipping_address')
                            Alamat pengiriman diubah
                        @else
                            {{ ucfirst(str_replace('_', ' ', (string) $activity->description)) }}
                        @endif
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        Oleh <strong>{{ $causer }}</strong> · {{ optional($activity->created_at)->diffForHumans() }}
                        @if ($activity->created_at)
                            ({{ $activity->created_at->format('d M Y, H:i') }})
                        @endif
                    </span>

                    {{-- Catatan tambahan (mis. alasan batal) --}}
                    @if ($props->has('alasan'))
                        <span class="mt-1 inline-block rounded bg-red-50 px-2 py-1 text-xs text-red-700 dark:bg-red-900/30 dark:text-red-300">
                            Alasan: {{ $props->get('alasan') }}
                        </span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
@else
    <p class="text-sm text-gray-400 italic">Belum ada aktivitas tercatat untuk pesanan ini.</p>
@endif
