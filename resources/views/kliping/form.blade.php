@extends('layouts.app')

@section('title', 'Generate Kliping | e-Kliping Kabupaten Buleleng')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
  <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 relative">
    <h1 class="text-3xl font-semibold text-gray-800 mb-8 border-b border-gray-300 pb-4 text-center">
      Generate Kliping 
    </h1>

    @if(session('error'))
      <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 border border-red-300 text-base text-center">
        {{ session('error') }}
      </div>
    @endif

    @if($portals->isEmpty())
      <p class="text-center text-gray-500">Tidak ada portal tersedia.</p>
    @else
      <form action="{{ route('kliping.generate') }}" method="POST" class="space-y-8">
        @csrf

        {{-- Pilihan Portal --}}
        <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
          <label for="portal" class="col-span-3 text-gray-900 font-semibold">
            Pilih Portal
          </label>

          <select 
            name="portal" 
            id="portal"
            class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          >
            @foreach($portals as $portal)
              <option value="{{ $portal }}">{{ ucfirst($portal) }}</option>
            @endforeach
          </select>
        </div>

        {{-- Tanggal --}}
        <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
          <label for="tanggal" class="col-span-3 text-gray-900 font-semibold">
            Tanggal
          </label>

          <input 
            type="date" 
            name="tanggal" 
            id="tanggal" 
            required
            class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        {{-- Tombol Submit --}}
        <div class="flex justify-end pt-4">
          <button 
            type="submit" 
            class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-red-500 transition"
          >
            Generate PDF
          </button>
        </div>
      </form>
    @endif
  </div>
</div>
@endsection
