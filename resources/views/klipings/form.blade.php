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

    @if(session('success'))
      <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 border border-green-300 text-base text-center">
        {{ session('success') }}
      </div>
    @endif

    <form 
      action="{{ $isEdit ? route('klipings.update', $kliping->id) : route('klipings.store') }}" 
      method="POST" 
      enctype="multipart/form-data" 
      class="space-y-8"
    >
      @csrf
      @if($isEdit)
        @method('PUT')
      @endif

      {{-- Nama Media --}}
      <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="nama_media" class="col-span-3 text-gray-900 font-semibold">
          Nama Media
        </label>
        <select 
          name="nama_media" 
          id="nama_media"
          class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        >
          @php
            $medias = ['Koran Buleleng', 'Radar Bali', 'Bali Post', 'Tribun Bali', 'Nusa Bali'];
          @endphp
          @foreach($medias as $media)
            <option value="{{ $media }}" 
              {{ old('nama_media', $isEdit ? $kliping->nama_media : '') == $media ? 'selected' : '' }}>
              {{ $media }}
            </option>
          @endforeach
        </select>
        @error('nama_media')
          <p class="col-span-12 text-red-600 mt-1 text-sm">{{ $message }}</p>
        @enderror
      </div>

      {{-- Media Type --}}
      <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="media_type" class="col-span-3 text-gray-900 font-semibold">
          Tipe Media
        </label>
        <select 
          name="media_type" 
          id="media_type"
          class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        >
          <option value="">Pilih Media</option>
          <option value="online" {{ old('media_type', $isEdit ? $kliping->media_type : '') == 'online' ? 'selected' : '' }}>
            Online
          </option>
          <option value="offline" {{ old('media_type', $isEdit ? $kliping->media_type : '') == 'offline' ? 'selected' : '' }}>
            Offline
          </option>
        </select>
        @error('media_type')
          <p class="col-span-12 text-red-600 mt-1 text-sm">{{ $message }}</p>
        @enderror
      </div>


      {{-- Kategori --}}
      <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="kategori" class="col-span-3 text-gray-900 font-semibold">
          Kategori
        </label>
        <select 
          name="kategori" 
          id="kategori"
          class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        >
          @php
            $categories = ['Olahraga', 'Pendidikan', 'Pemerintahan', 'Kesehatan', 'Ekonomi'];
          @endphp
          @foreach($categories as $category)
            <option value="{{ $category }}" 
              {{ old('kategori', $isEdit ? $kliping->kategori : '') == $category ? 'selected' : '' }}>
              {{ $category }}
            </option>
          @endforeach
        </select>
        @error('kategori')
          <p class="col-span-12 text-red-600 mt-1 text-sm">{{ $message }}</p>
        @enderror
      </div>

      {{-- Sub Kategori --}}
      <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="sub_kategori" class="col-span-3 text-gray-900 font-semibold">
          Sub Kategori
        </label>

        <input
          type="text"
          name="sub_kategori"
          id="sub_kategori"
          value="{{ old('sub_kategori', $kliping->sub_kategori ?? '') }}"
          placeholder="Isi sub kategori"
          class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base 
                focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
        @error('sub_kategori')
          <p class="col-span-12 text-red-600 mt-1 text-sm">{{ $message }}</p>
        @enderror
      </div>
      
      {{-- Tanggal Berita --}}
      <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="tanggal" class="col-span-3 text-gray-900 font-semibold">
          Tanggal Berita
        </label>
        <input
          type="date"
          name="tanggal"
          id="tanggal"
          value="{{ old('tanggal', $isEdit ? $kliping->tanggal : '') }}"
          class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
          />
        @error('tanggal')
          <p class="col-span-12 text-red-600 mt-1 text-sm">{{ $message }}</p>
        @enderror
      </div>

      {{-- Sentimen --}}
      <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="sentimen" class="col-span-3 text-gray-900 font-semibold">
          Sentimen
        </label>
        <select 
          name="klasifikasi" 
          id="klasifikasi"
          class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-900 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Pilih (Otomatis)</option>
          <option value="Positif" {{ old('sentimen', $isEdit ? $kliping->klasifikasi : '') == 'Positif' ? 'selected' : '' }}>Positif</option>
          <option value="Netral" {{ old('sentimen', $isEdit ? $kliping->klasifikasi : '') == 'Netral' ? 'selected' : '' }}>Netral</option>
          <option value="Negatif" {{ old('sentimen', $isEdit ? $kliping->klasifikasi : '') == 'Negatif' ? 'selected' : '' }}>Negatif</option>

        </select>
        @error('sentimen')
          <p class="col-span-12 text-red-600 mt-1 text-sm">{{ $message }}</p>
        @enderror
      </div>

      {{-- Gambar --}}
      <div id="gambar_field" class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="gambar" class="col-span-3 text-gray-900 font-semibold">
          Upload Gambar (opsional)
        </label>
        <div class="col-span-9">
          <input
            type="file"
            name="gambar"
            id="gambar"
            accept="image/*"
            class="w-full text-gray-800 text-base"
          />
          @error('gambar')
            <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
          @enderror

          {{-- Preview gambar lama kalau ada --}}
          @if(!empty($berita->gambar))
            <div class="mt-2">
              <p class="text-gray-700 text-sm mb-1">Gambar lama:</p>
              <img src="{{ asset('storage/' . $berita->gambar) }}" 
                  alt="Gambar Lama" 
                  class="w-32 h-auto rounded-md border">
            </div>
          @endif
        </div>
      </div>

      {{-- Judul Berita --}}
      <div id="judul_field" class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="judul" class="col-span-3 text-gray-900 font-semibold">
          Judul Berita
        </label>
        <input
        type="text"
        name="judul"
        id="judul"
        value="{{ old('judul', $isEdit ? $kliping->judul : '') }}"
        placeholder="Masukkan judul berita"
        class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
        @error('judul')
          <p class="col-span-12 text-red-600 mt-1 text-sm">{{ $message }}</p>
        @enderror
      </div>

      {{-- Isi Berita --}}
      <div id="isi_field" class="grid grid-cols-12 items-start gap-4 border-b border-gray-300 pb-4">
        <label for="isi" class="col-span-3 text-gray-900 font-semibold pt-3">
          Isi Berita
        </label>
        <textarea
          name="isi"
          id="isi"
          rows="5"
          placeholder="Masukkan isi berita (opsional)"
          class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
        >{{ old('isi', $kliping->isi ?? '') }}</textarea>
        @error('isi')
          <p class="col-span-12 text-red-600 mt-1 text-sm">{{ $message }}</p>
        @enderror
      </div>

      {{-- Link Berita --}}
      <div id="link_field" class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="link" class="col-span-3 text-gray-900 font-semibold">
          Link Berita
        </label>
       <input
        type="url"
        name="link"
        id="link"
        value="{{ old('link', $kliping->link ?? '') }}"
        placeholder="Masukkan URL berita"
        class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
        @error('link')
          <p class="col-span-12 text-red-600 mt-1 text-sm">{{ $message }}</p>
        @enderror
      </div>

      {{-- Submit Button --}}
      <div class="flex justify-end pt-4">
        <button 
          type="submit" 
          class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-red-500 transition"
        >
          {{ $isEdit ? 'Update' : 'Generate' }}
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Script toggle field --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const mediaType = document.getElementById('media_type');
    const judulField = document.getElementById('judul_field');
    const isiField = document.getElementById('isi_field');
    const linkField = document.getElementById('link_field');
    const gambarField = document.getElementById('gambar_field');

    function toggleFields() {
      if (mediaType.value === 'online') {
        judulField.style.display = '';
        isiField.style.display = '';
        linkField.style.display = '';
        gambarField.style.display = ''; // tetap ada tapi opsional
      } else if (mediaType.value === 'offline') {
        judulField.style.display = 'none';
        isiField.style.display = 'none';
        linkField.style.display = 'none';
        gambarField.style.display = ''; // wajib diisi
      } else {
        // default kalau belum pilih
        judulField.style.display = '';
        isiField.style.display = '';
        linkField.style.display = '';
        gambarField.style.display = '';
      }
    }

    mediaType.addEventListener('change', toggleFields);
    toggleFields();
  });
</script>
@endsection
