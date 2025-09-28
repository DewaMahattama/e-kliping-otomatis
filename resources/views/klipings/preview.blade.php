@extends('layouts.app')

@section('title', 'Preview Kliping')

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4">
    <h1 class="text-2xl font-bold mb-4 text-center">Preview Kliping</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 text-center">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tombol Edit --}}
    <div class="mb-4 flex justify-end">
        <a href="{{ route('klipings.edit', $kliping->id) }}" 
        class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded">
        Edit 
        </a>
    </div>

    {{-- Preview PDF --}}
    <iframe 
        src="{{ $pdfUrl }}" 
        width="100%" 
        height="800px" 
        style="border: 1px solid #ccc;">
    </iframe>

    {{-- Tombol Kembali & Download --}}
    <div class="mt-6 flex justify-between">
        {{-- Tombol Kembali --}}
        <a href="{{ route('klipings.form') }}" 
        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
            Kembali
        </a>

        {{-- Tombol Download --}}
        <a href="{{ $pdfUrl }}" 
        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded"
        download>
        Download
        </a>
    </div>
@endsection
