<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('klipings', function (Blueprint $table) {
            $table->id();

            // ENUM Nama Media
            $table->enum('nama_media', [
                'Koran Buleleng',
                'Radar Bali',
                'Bali Post',
                'Tribun Bali',
                'Nusa Bali'
            ]);

            // ENUM Kategori
            $table->enum('kategori', [
                'Olahraga',
                'Pendidikan',
                'Pemerintahan',
                'Kesehatan',
                'Ekonomi'
            ]);

            // Sub kategori bebas isi
            $table->string('sub_kategori')->nullable();

            // Input teks/manual
            $table->string('judul')->nullable();
            $table->text('isi')->nullable();
            $table->string('link')->nullable();

            // Upload gambar (offline)
            $table->string('gambar_path')->nullable();

            // Hasil OCR (kalau offline/gambar)
            $table->longText('ocr_text')->nullable();

            // Hasil analisis sentimen
            $table->string('klasifikasi')->nullable();

            // Tipe media (online / offline)
            $table->enum('media_type', ['online', 'offline'])->default('online');

            // Path file PDF hasil generate
            $table->string('pdf_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klipings');
    }
};
