<x-layout title="PepeCoin Wealth - Top 50 Addresses - peppool.space" og_image="pepecoin-wealth.png">
    <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-400">
        <h1 class="text-2xl md:text-4xl font-bold text-gray-900 dark:text-gray-300 mb-3 md:mb-4">PepeCoin Wealth</h1>
        <p class="text-sm md:text-base">Top 50 addresses by balance</p>
    </div>

    <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-200/70 rounded-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm md:text-base">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 md:px-8 md:py-4 text-left text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-4 md:px-8 md:py-4 text-left text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wider">Address</th>
                        <th scope="col" class="px-6 py-4 md:px-8 md:py-4 text-right text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wider">Balance</th>
                        <th scope="col" class="px-6 py-4 md:px-8 md:py-4 text-right text-xs md:text-sm font-semibold text-gray-600 uppercase tracking-wider">Approx</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($addresses as $idx => $row)
                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-emerald-50/60">
                            <td class="px-6 py-4 md:px-8 md:py-4 text-gray-700">{{ $idx + 1 }}</td>
                            <td class="px-6 py-4 md:px-8 md:py-4 font-mono">
                                <a href="{{ route('address.show', $row->address) }}" class="text-emerald-700 hover:text-emerald-800 underline underline-offset-2">
                                    {{ $row->address }}
                                </a>
                            </td>
                            <td class="px-6 py-4 md:px-8 md:py-4 text-right font-semibold text-gray-900">
                                @php
                                    $val = (int) $row->balance;
                                    if ($val >= 1_000_000_000_000) {
                                        $human = \Illuminate\Support\Number::format(floor($val / 1_000_000_000_000), maxPrecision: 0) . ' Trillion';
                                    } elseif ($val >= 1_000_000_000) {
                                        $human = \Illuminate\Support\Number::format(floor($val / 1_000_000_000), maxPrecision: 0) . ' Billion';
                                    } elseif ($val >= 1_000_000) {
                                        $human = \Illuminate\Support\Number::format(floor($val / 1_000_000), maxPrecision: 0) . ' Million';
                                    } elseif ($val >= 1_000) {
                                        $human = \Illuminate\Support\Number::format(floor($val / 1_000), maxPrecision: 0) . ' Thousand';
                                    } else {
                                        $human = \Illuminate\Support\Number::format($val, maxPrecision: 0);
                                    }
                                @endphp
                                <span class="font-mono">{{ \Illuminate\Support\Number::format($val, maxPrecision: 0) }}</span>
                            </td>
                            <td class="px-6 py-4 md:px-8 md:py-4 text-right text-gray-700">
                                <span class="font-normal">{{ $human }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 md:px-8 md:py-10 text-center text-gray-500">No data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
