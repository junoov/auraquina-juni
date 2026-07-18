@php
    $pesanans = $getState();
@endphp

@if($pesanans->isEmpty())
    <div class="text-sm text-gray-500 py-4 text-center">
        Belum ada riwayat pesanan untuk pelanggan ini.
    </div>
@else
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 font-semibold">Kode Pesanan</th>
                    <th scope="col" class="px-6 py-3 font-semibold">Tanggal</th>
                    <th scope="col" class="px-6 py-3 font-semibold">Total</th>
                    <th scope="col" class="px-6 py-3 font-semibold">Status</th>
                    <th scope="col" class="px-6 py-3 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($pesanans as $pesanan)
                    <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                            <span class="font-mono text-xs font-bold px-2 py-1 bg-amber-50 dark:bg-amber-950/30 text-amber-900 dark:text-amber-300 rounded">
                                {{ $pesanan->kode_pesanan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $pesanan->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white font-semibold">
                            Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusLabel = match($pesanan->status) {
                                    'pending_payment' => 'Menunggu Pembayaran',
                                    'paid' => 'Sudah Dibayar',
                                    'processing' => 'Diproses',
                                    'packed' => 'Dikemas',
                                    'shipped' => 'Dikirim',
                                    'delivered' => 'Diterima',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                    'expired' => 'Kedaluwarsa',
                                    'return_requested' => 'Ajukan Retur',
                                    'refunded' => 'Dikembalikan',
                                    default => ucfirst(str_replace('_', ' ', $pesanan->status))
                                };
                                $statusColor = match($pesanan->status) {
                                    'pending_payment' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                    'paid', 'processing', 'packed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                    'shipped' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
                                    'delivered', 'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                    'cancelled', 'expired', 'refunded' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300'
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('filament.admin.resources.pesanans.view', ['record' => $pesanan]) }}" 
                               class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 hover:text-amber-800 dark:text-amber-500 dark:hover:text-amber-400 hover:underline">
                                Lihat Pesanan
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
