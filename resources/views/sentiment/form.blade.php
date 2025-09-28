@extends('layouts.app')

@section('title', 'Analisis Sentimen | e-Kliping Kabupaten Buleleng')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
  <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
    <h1 class="text-3xl font-semibold text-gray-800 mb-8 text-center">Form Analisis Sentimen</h1>

    {{-- Notifikasi error atau sukses --}}
    @if(session('error'))
      <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
      </div>
    @endif
    @if(session('status'))
      <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('status') }}
      </div>
    @endif

    <form action="{{ route('sentiment.analyze') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf

      {{-- Input teks manual --}}
      <div>
        <label for="text" class="block text-sm font-semibold text-gray-700 mb-1">Teks (Opsional)</label>
        <textarea id="text" name="text" rows="5"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500 resize-none"
        >{{ old('text') }}</textarea>
        <p class="text-xs text-gray-500 mt-1">*Jika tidak mengisi teks, bisa upload gambar untuk di-OCR.</p>
      </div>

      {{-- Upload gambar --}}
      <div>
        <label for="image" class="block text-sm font-semibold text-gray-700 mb-1">Upload Gambar (Opsional)</label>
        <input type="file" id="image" name="image"
          class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
        <p class="text-xs text-gray-500 mt-1">*Format yang didukung: JPG, PNG. Maksimal 2MB.</p>
      </div>

      {{-- Tombol Aksi --}}
      <div class="flex justify-end gap-4 pt-4">
        <button type="submit"
          class="inline-block px-6 py-2 bg-red-600 text-white font-semibold rounded-lg shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
          Analisis
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
