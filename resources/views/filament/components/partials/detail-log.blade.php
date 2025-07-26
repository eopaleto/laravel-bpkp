<!-- resources/views/filament/components/partials/detail-log.blade.php -->

<div class="space-y-2">
    <table class="table-auto w-full text-sm text-left">
        <thead>
            <tr>
                <th class="border px-4 py-2">Nama Petugas</th>
                <th class="border px-4 py-2">Jumlah</th>
                <th class="border px-4 py-2">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $item)
                <tr>
                    <td class="border px-4 py-2">{{ $item->user->name ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ $item->jumlah }}</td>
                    <td class="border px-4 py-2">{{ $item->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
