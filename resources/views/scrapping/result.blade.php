@extends('layouts.app')

@section('title', 'Hasil Scraping | e-kliping Kabupaten Buleleng')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-3xl font-semibold text-gray-900 mb-6">
        Hasil Scraping
    </h1>

    @if(empty($results) || count($results) === 0)
        <div class="bg-yellow-100 text-yellow-700 p-4 rounded">
            Tidak ada data ditemukan.
        </div>
    @else
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-sm text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold">Judul</th>
                        <th class="px-6 py-3 text-left font-bold">Konten</th>
                        <th class="px-6 py-3 text-left font-bold">Tanggal</th>
                        <th class="px-6 py-3 text-left font-bold">Portal</th>
                        <th class="px-6 py-3 text-left font-bold">URL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($results as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 w-1/5">
                                {{ $item->title ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 w-2/5">
                                {{ \Illuminate\Support\Str::limit($item->content ?? '-', 200, '...') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap w-1/6">
                                {{ $item->tanggal ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $item->portal ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                @if(!empty($item->url))
                                    <a href="{{ $item->url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                        Lihat Artikel
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Form Download -->
        <form method="GET" action="{{ route('scrapping.download') }}">
            @csrf
            @foreach($results as $row)
                <input type="hidden" name="ids[]" value="{{ $row->id }}">
            @endforeach

            <div class="flex justify-between mt-6">
                <!-- Tombol kembali -->
                <a href="{{ route('scrapping.formScrapping') }}"
                   class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium">
                   Kembali
                </a>

                <!-- Tombol download -->
                <button type="submit"
                        class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium">
                    Download
                </button>
            </div>
        </form>
    @endif
</div>
@endsection
