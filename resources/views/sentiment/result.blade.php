{{-- resources/views/sentiment/result.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
  <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8">
    <h1 class="text-3xl font-semibold text-gray-800 mb-8 text-center">Hasil Analisis Sentimen</h1>

    {{-- Teks yang Dianalisis --}}
    <section class="mb-8">
      <h2 class="text-lg font-semibold text-gray-700 mb-3">Teks yang Dianalisis</h2>
      <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 text-gray-800 leading-relaxed text-justify">
        {!! nl2br(e($text)) !!}
      </div>
    </section>

    {{-- Hasil Model --}}
    <section class="mb-10">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Hasil Model</h2>

      @php
        $resultData = json_decode($result, true);
        $label = ucfirst($resultData['label'] ?? '');
        $score = number_format($resultData['score'] ?? 0, 4);

        $badgeColor = match(strtolower($label)) {
          'positif' => 'bg-green-100 text-green-700',
          'negatif' => 'bg-red-100 text-red-700',
          'netral'  => 'bg-gray-100 text-gray-700',
          default   => 'bg-gray-100 text-gray-700'
        };
      @endphp

      {{-- Label Sentimen dan Score --}}
      <div class="flex items-center gap-4 mb-5">
        <span class="px-5 py-2 text-sm font-semibold rounded-full {{ $badgeColor }}">
          {{ $label }}
        </span>
        <span class="text-sm text-gray-500">Score: {{ $score }}</span>
      </div>

      {{-- Progress Bar Score --}}
      <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
          <div class="h-4 rounded-full transition-all duration-300 
              @if (($resultData['score'] ?? 0) < 0.5) bg-red-600 
              @elseif (($resultData['score'] ?? 0) < 0.8) bg-yellow-200 
              @else bg-blue-600 @endif"
              style="width: {{ ($resultData['score'] ?? 0) * 100 }}%;">
          </div>
      </div>

      
    {{-- Tombol Kembali --}}
    <div class="text-right mt-4">
      <a href="{{ route('sentiment.form') }}"
        class="inline-block px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
        Kembali
      </a>
    </div>
    </div>
  </div>
</div>
@endsection
