@if ($logs->isEmpty())
    <p class="text-sm text-gray-400">Belum ada riwayat login.</p>
@else
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="text-xs uppercase text-gray-400">
                <tr>
                    <th class="py-2 pr-4">Waktu</th>
                    <th class="py-2 pr-4">Metode</th>
                    <th class="py-2 pr-4">Status</th>
                    <th class="py-2 pr-4">Durasi</th>
                    <th class="py-2 pr-4">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach ($logs as $log)
                    <tr>
                        <td class="py-2 pr-4 text-gray-600">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="py-2 pr-4 uppercase">{{ $log->method }}</td>
                        <td class="py-2 pr-4">
                            <span @class([
                                'rounded px-2 py-0.5 text-xs font-medium',
                                'bg-green-100 text-green-700' => $log->status === 'success',
                                'bg-red-100 text-red-700' => $log->status === 'failed',
                            ])>{{ $log->status }}</span>
                        </td>
                        <td class="py-2 pr-4 text-gray-600">{{ $log->duration_ms ? $log->duration_ms.' ms' : '-' }}</td>
                        <td class="py-2 pr-4 text-gray-400">{{ $log->ip_address }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
