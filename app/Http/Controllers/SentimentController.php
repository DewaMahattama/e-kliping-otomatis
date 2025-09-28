<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class SentimentController extends Controller
{
    public function index()
    {
        return view('sentiment.form');
    }

    public function analyze(Request $request)
    {
        $text = '';

        // OCR from image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('images', 'public');
            $imagePath = storage_path('app/public/' . $path);

            if (!file_exists($imagePath)) {
                return back()->with('error', 'Gambar tidak ditemukan: ' . $imagePath);
            }

            try {
                $ocr = new \thiagoalessio\TesseractOCR\TesseractOCR($imagePath);
                $text = $ocr->run();
                \Log::info('Teks hasil OCR:', ['text' => $text]);
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal melakukan OCR: ' . $e->getMessage());
            }
        }

        // Text input fallback
        if (empty($text) && $request->filled('text')) {
            $text = $request->input('text');
        }

        // Validate text
        if (empty($text)) {
            return back()->with('error', 'Teks tidak boleh kosong.');
        }

        // Membatasi teks hingga 374 kata pertama
        $words = preg_split('/\s+/', trim($text));
        $limitedText = implode(' ', array_slice($words, 0, 374));

        // Tambahan: batasi juga maksimal 2000 karakter
        $limitedText = substr($limitedText, 0, 2000);

        try {
            // Panggil API Flask lokal sekali saja, POST ke /predict
            $response = Http::timeout(60)->post('http://127.0.0.1:5001/analyze', [
                'text' => $limitedText  // Kirim teks yang dibatasi
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Gagal menghubungi API Flask: ' . $response->body());
            }

            $resultData = $response->json();

            \Log::info('Hasil prediksi dari API Flask:', ['result' => $resultData]);

            return view('sentiment.result', [
                'text' => $limitedText,  // Tampilkan teks yang dibatasi
                'result' => json_encode($resultData, JSON_PRETTY_PRINT),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghubungi API Flask: ' . $e->getMessage());
        }
    }
}
