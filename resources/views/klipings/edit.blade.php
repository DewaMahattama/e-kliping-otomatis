<!-- @extends('layouts.app')

@section('title', 'Edit Kliping')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-bold mb-6">Edit Kliping</h1>

  <form action="{{ route('klipings.update', $kliping->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Nama Media --}}
    <div>
      <label for="nama_media" class="font-semibold">Nama Media</label>
      <select name="nama_media" id="nama_media" class="w-full border rounded p-2">
        @php
          $medias = ['Koran Buleleng', 'Radar Bali', 'Bali Post', 'Tribun Bali', 'Nusa Bali'];
        @endphp
        @foreach($medias as $media)
          <option value="{{ $media }}" {{ $kliping->nama_media == $media ? 'selected' : '' }}>{{ $media }}</option>
        @endforeach
      </select>
    </div>

    {{-- Tanggal --}}
    <div>
      <label for="tanggal" class="font-semibold">Tanggal</label>
      <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $kliping->tanggal) }}" class="w-full border rounded p-2">
    </div>

    {{-- Judul --}}
    <div>
      <label for="judul" class="font-semibold">Judul</label>
      <input type="text" name="judul" id="judul" value="{{ old('judul', $kliping->judul) }}" class="w-full border rounded p-2">
    </div>

    {{-- Isi --}}
    <div>
      <label for="isi" class="font-semibold">Isi</label>
      <textarea name="isi" id="isi" rows="5" class="w-full border rounded p-2">{{ old('isi', $kliping->isi) }}</textarea>
    </div>

    {{-- Gambar --}}
    <div>
      <label for="gambar" class="font-semibold">Gambar (Opsional)</label>
      <input type="file" name="gambar" id="gambar" class="w-full">
      @if($kliping->gambar_path)
        <p class="mt-2 text-sm text-gray-600">Gambar saat ini:</p>
        <img src="{{ asset('storage/'.$kliping->gambar_path) }}" alt="gambar" class="w-40 mt-2">
      @endif
    </div>

    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
      Simpan Perubahan
    </button>
  </form>
</div>
@endsection -->
