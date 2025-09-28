<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kliping PDF - {{ $berita->title }}</title>
    <style>
        /* Pakai Times New Roman */
        @font-face {
            font-family: 'Times New Roman';
            src: local('Times New Roman'), local('TimesNewRoman'), serif;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            color: #000;
            margin: 40px 50px;
            line-height: 1.4;
        }

        h1 {
            font-weight: bold;
            font-size: 22px;
            text-align: center;
            margin-bottom: 30px;
        }

        .meta {
            margin-bottom: 20px;
            font-size: 14px;
        }

        .meta strong {
            display: inline-block;
            width: 80px;
            vertical-align: top;
        }

        .meta a {
            color: black;
            text-decoration: underline;
            word-break: break-all;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 20px 0;
        }

        .content {
            font-size: 14px;
            text-align: justify;
            white-space: pre-line; /* supaya new line di content tampil */
        }

        .footer-source {
            margin-top: 40px;
            font-size: 12px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <h1>{{ $berita->title }}</h1>

    <div class="meta">
        <div><strong>Portal:</strong> {{ $berita->portal }}</div>
        <div><strong>Tanggal:</strong> {{ $tanggal_formatted ?? $berita->tanggal }}</div>
        <div><strong>Sentimen:</strong> {{ $sentimen }}</div>
    </div>

    <hr>

    <div class="content">
        {!! nl2br(e($berita->content)) !!}
    </div>

    <div class="footer-source">
        Sumber berita: <a href="{{ $berita->url }}">{{ $berita->url }}</a>
    </div>

</body>
</html>
