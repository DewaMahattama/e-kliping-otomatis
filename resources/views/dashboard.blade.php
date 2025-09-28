@extends('layouts.app')

@section('content')
<div class="relative flex flex-col items-center justify-start min-h-[70vh] bg-gray-100 px-4 pt-20 text-center overflow-hidden">
    <!-- Background gradient blur -->
    <div class="absolute inset-0 bg-gradient-to-tr from-white via-blue-100 to-pink-200 opacity-30 blur-3xl pointer-events-none"></div>

    <!-- Konten utama -->
    <div class="relative z-10">
        <h1 class="text-5xl font-bold text-gray-900 mb-4 leading-tight">
            Jendela Informasi <br />
            Berita Terkini Daerah Buleleng
        </h1>
        <p class="text-gray-600 max-w-xl mb-8 text-center mx-auto">
          Website ini menyajikan hasil kliping berita terkini dan terpercaya dari berbagai sumber di daerah Buleleng, sehingga dapat melihat perkembangan informasi lokal secara cepat dan akurat.
        </p>

        <div class="flex space-x-8 justify-center">
            <!-- Ganti src dengan path logo kamu -->
            <img src="{{ asset('images/lambang.png') }}" alt="Logo 1" class="h-16 w-auto">
            <img src="{{ asset('images/komdigibll.png') }}" alt="Logo 2" class="h-16 w-auto">
        </div>
    </div>
</div>
@endsection

