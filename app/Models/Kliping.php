<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kliping extends Model
{
    protected $fillable = [
        'nama_media', 'kategori', 'sub_kategori',
        'judul', 'isi', 'link', 'gambar_path',
        'ocr_text', 'klasifikasi', 'media_type', 'pdf_path', 'tanggal'
    ];
}
