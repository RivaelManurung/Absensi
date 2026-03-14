@props([
    'headers' => [],
    'rows' => [],
    'empty' => 'No data available.',
])

<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50">
            <tr>
                @foreach ($headers as $header)
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse ($rows as $row)
                <tr class="hover:bg-sky-50/40 transition-colors">
                    @foreach ($row as $value)
                        <td class="px-4 py-3 text-slate-700">{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-4 py-6 text-center text-slate-500">
                        {{ $empty }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
