<!-- @extends('layouts.app')

@section('content')
<h1>Detail Kliping</h1>

<p><strong>Nama Media:</strong> {{ $kliping->nama_media }}</p>
<p><strong>Media Type:</strong> {{ $kliping->media_type }}</p>
<p><strong>Kategori:</strong> {{ $kliping->kategori }}</p>
<p><strong>Sub Kategori:</strong> {{ $kliping->sub_kategori ?? '-' }}</p>

@if($kliping->media_type === 'offline' && $kliping->gambar_path)
    <p><strong>Gambar:</strong></p>
    <img src="{{ asset('storage/' . $kliping->gambar_path) }}" alt="Gambar Kliping" style="max-width:300px;">
    <p><strong>OCR Text:</strong> {{ $kliping->ocr_text }}</p>
@else
    <p><strong>Judul:</strong> {{ $kliping->judul }}</p>
    <p><strong>Isi:</strong> {{ $kliping->isi }}</p>
    <p><strong>Link:</strong> <a href="{{ $kliping->link }}" target="_blank">{{ $kliping->link }}</a></p>
@endif

<p><strong>Sentimen/Klasifikasi:</strong> {{ $kliping->klasifikasi }}</p>

@if($kliping->pdf_path)
    <a href="{{ route('klipings.preview', $kliping->id) }}" target="_blank">Preview PDF</a> |
    <a href="{{ route('klipings.download', $kliping->id) }}">Download PDF</a>
@endif

<a href="{{ route('klipings.form') }}">Tambah Kliping Baru</a>
@endsection -->
