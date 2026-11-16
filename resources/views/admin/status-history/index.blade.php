@extends('layouts.operator')

@section('page_title', 'Riwayat Perubahan Status')

@section('content')
    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm ring-1 ring-bank-light">
        <table class="min-w-full divide-y divide-bank-light text-sm">
            <thead class="bg-bank-bg text-left text-xs uppercase text-bank-gray">
                <tr>
                    <th scope="col" class="px-4 py-3">Waktu</th>
                    <th scope="col" class="px-4 py-3">Tamu</th>
                    <th scope="col" class="px-4 py-3">Petugas</th>
                    <th scope="col" class="px-4 py-3">Tindakan</th>
                    <th scope="col" class="px-4 py-3">Perubahan status</th>
                    <th scope="col" class="px-4 py-3">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-bank-light">
                @forelse($history as $item)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3">{{ $item->created_at?->format('d/m/Y H:i:s') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-bank-navy">{{ $item->visit?->visitor_name ?? 'Data telah dihapus' }}</span>
                            @if($item->visit)<span class="block text-xs text-bank-gray">{{ $item->visit->visitor_institution }}</span>@endif
                        </td>
                        <td class="px-4 py-3">{{ $item->user?->name ?? 'Sistem / akun dihapus' }}</td>
                        <td class="px-4 py-3">{{ str_replace('-', ' ', $item->action) }}</td>
                        <td class="px-4 py-3">{{ $item->from_status ? ucfirst($item->from_status).' → ' : '' }}{{ ucfirst($item->to_status) }}</td>
                        <td class="max-w-xs px-4 py-3">{{ $item->note ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-bank-gray">Belum ada riwayat status.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $history->links() }}</div>
@endsection
