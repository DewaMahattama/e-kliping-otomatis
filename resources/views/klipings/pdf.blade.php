{{-- resources/views/kliping/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kliping Edisi {{ $tanggal ?? '' }}</title>
    <style>
         body {
            font-family: "Times New Roman", Times, serif;
            margin: 40px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 80px;
            margin-bottom: 5px;
        }
        .judul-header {
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
        }

        /* Table info dengan garis atas & bawah */
        .table-info {
            width: 100%;
            margin: 15px 0;
            font-size: 12px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            border-collapse: collapse;
        }
        .table-info td {
            padding: 4px 8px;
            vertical-align: top;
        }

        .judul-berita {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
        }

        .konten {
            font-size: 10pt;
            text-align: justify;
        }

        .konten p {
            text-indent: 1cm;
            margin: 0; /* hapus spasi antar paragraf */
        }

        .konten img {
            display: block;
            margin: 15px auto; /* Menempatkan gambar di tengah */
            max-width: 100%; /* Membuat gambar responsif sesuai lebar kontainer */
            height: auto;    /* Mempertahankan rasio gambar */
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 40px;
            padding-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <img src="{{ public_path('images/lambang.png') }}" alt="Logo Kabupaten Buleleng">
        <div class="judul-header">
            KLIPING BERITA MEDIA CETAK-ONLINE KABUPATEN BULELENG
        </div>
    </div>

    {{-- Info --}}
    <table class="table-info">
        <tr>
            <td width="150">Nama Media</td>
            <td>:{{ $kliping->nama_media ?? '' }}</td>
            <td width="150">Kategori</td>
            <td>: {{ $kliping->kategori ?? '' }}</td>
        </tr>
        <tr>
            <td>Klasifikasi Berita</td>
            <td>: {{ $kliping->klasifikasi ?? '' }}</td>
            <td>Sub. Kategori</td>
            <td>: {{ $kliping->sub_kategori ?? '' }}</td>
        </tr>
    </table>

    {{-- Judul --}}
    <div class="judul-berita">
        {{ $kliping->judul ?? '' }}
    </div>

    {{-- Tanggal hanya untuk media online --}}
    @if($kliping->media_type === 'online' && !empty($kliping->tanggal))
        <div class="tanggal-berita" style="font-size: 12px; font-style: italic; margin-top: 4px; text-align: center;">
            {{ \Carbon\Carbon::parse($kliping->tanggal)->translatedFormat('l, d F Y') }}
        </div>
    @endif


    {{-- Konten --}}
    <div class="konten">
        {{-- Media Online --}}
        @if($kliping->media_type === 'online')
            @if(isset($base64Image))
                <img src="{{ $base64Image }}" alt="Gambar Berita">
            @endif
            {!! nl2br(e($kliping->isi)) !!}
            <p><strong>Sumber:</strong> <a href="{{ $kliping->link }}">{{ $kliping->link }}</a></p>
        @endif

        {{-- Media Offline --}}
        @if($kliping->media_type === 'offline' && isset($base64Image))
            <img src="{{ $base64Image }}" alt="Scan Koran">
        @endif
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="sub">Bidang Pengelolaan dan Layanan Informasi Publik</div>
        <div class="main">DINAS KOMUNIKASI, INFORMATIKA, PERSANDIAN DAN STATISTIK KABUPATEN BULELENG</div>
    </div>
</body>
</html>