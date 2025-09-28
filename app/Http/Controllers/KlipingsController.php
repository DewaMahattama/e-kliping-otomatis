<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\Kliping;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class KlipingsController extends Controller
{
    public function create()
    {
        $isEdit = false;
        return view('klipings.form', compact('isEdit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_media'   => 'required|in:Koran Buleleng,Radar Bali,Bali Post,Tribun Bali,Nusa Bali',
            'media_type'   => 'required|in:online,offline',
            'kategori'     => 'required|in:Olahraga,Pendidikan,Pemerintahan,Kesehatan,Ekonomi',
            'sub_kategori' => 'nullable|string|max:255',
            'tanggal'      => 'required|date',
            'gambar'       => 'required|image|max:2048', // wajib utk semua
            'judul'        => 'required_if:media_type,online|nullable|string',
            'isi'          => 'nullable|string',
            'link'         => 'required_if:media_type,online|nullable|url',
            'klasifikasi'  => 'nullable|in:Positif,Netral,Negatif', // dropdown manual
        ]);

        $mediaType = $request->input('media_type');
        $data = $request->only([
            'nama_media',
            'kategori',
            'sub_kategori',
            'judul',
            'isi',
            'link',
            'tanggal'
        ]);
        $data['media_type'] = $mediaType;

        // Simpan gambar
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('kliping-images', 'public');
            $data['gambar_path'] = $path;
        }

        // Tentukan klasifikasi (manual / otomatis)
        if ($request->filled('sentimen')) {
            // Jika user pilih manual
            $data['klasifikasi'] = $request->input('sentimen');
        } else {
            // Jika otomatis
            if ($mediaType === 'offline') {
                // OCR
                $imagePath = storage_path('app/public/' . $data['gambar_path']);
                try {
                    $ocr = new \thiagoalessio\TesseractOCR\TesseractOCR($imagePath);
                    $ocrText = $ocr->run();
                    $data['ocr_text'] = $ocrText;
                } catch (\Exception $e) {
                    return back()->with('error', 'Gagal melakukan OCR: ' . $e->getMessage());
                }

                $limitedText = $this->limitText($ocrText);
                $data['klasifikasi'] = ucfirst($this->analyzeSentiment($limitedText));
            } else {
                // Online: analisis teks judul+isi
                $textToAnalyze = trim(($request->judul ?? '') . ' ' . ($request->isi ?? ''));
                $limitedText = $this->limitText($textToAnalyze);
                $data['klasifikasi'] = ucfirst($this->analyzeSentiment($limitedText));
            }
        }

        $kliping = Kliping::create($data);

        try {
            $base64Image = null;
            if ($kliping->gambar_path) {
                $base64Image = $this->imageToBase64($kliping->gambar_path);
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('klipings.pdf', [
                'kliping' => $kliping,
                'base64Image' => $base64Image
            ]);

            $pdfPath = 'kliping-pdf/kliping_' . $kliping->id . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());
            $kliping->update(['pdf_path' => $pdfPath]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }

        return redirect()->route('klipings.preview', ['id' => $kliping->id])
                        ->with('success', 'Kliping berhasil disimpan dan PDF berhasil dibuat.');
    }

    private function imageToBase64($relativePath)
    {
        $fullPath = storage_path('app/public/' . $relativePath);
        if (!file_exists($fullPath)) {
            return null;
        }
        $type = mime_content_type($fullPath);
        $data = file_get_contents($fullPath);
        return 'data:' . $type . ';base64,' . base64_encode($data);
    }
    public function preview($id)
    {
        $kliping = Kliping::findOrFail($id);

        if (!$kliping->pdf_path || !Storage::disk('public')->exists($kliping->pdf_path)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        // URL untuk embed PDF (route streaming)
        $pdfUrl = route('klipings.pdf', ['id' => $kliping->id]);

        return view('klipings.preview', compact('kliping', 'pdfUrl'));
    }

    public function previewPdf($id)
    {
        $kliping = Kliping::findOrFail($id);

        if (!$kliping->pdf_path || !Storage::disk('public')->exists($kliping->pdf_path)) {
            abort(404, 'PDF tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $kliping->pdf_path);

        return response()->file($filePath);
    }

    public function downloadPdf($id)
    {
        $kliping = Kliping::findOrFail($id);

        if (!$kliping->pdf_path || !Storage::disk('public')->exists($kliping->pdf_path)) {
            abort(404, 'PDF tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $kliping->pdf_path);

        return response()->download($filePath, 'kliping_' . $kliping->id . '.pdf');
    }

    // Fungsi pembantu untuk membatasi teks input
    private function limitText($text)
    {
        $words = preg_split('/\s+/', trim($text));
        $limitedText = implode(' ', array_slice($words, 0, 374));
        return substr($limitedText, 0, 2000);
    }

    // Fungsi pembantu untuk analisa sentimen via API
    private function analyzeSentiment($text)
    {
        try {
            $response = Http::timeout(60)->post('http://127.0.0.1:5001/analyze', ['text' => $text]);
            if ($response->failed()) {
                return 'API Gagal';
            }
            $resultData = $response->json();
            return $resultData['label'] ?? 'Tidak Terdeteksi';
        } catch (\Exception $e) {
            return 'Sentimen Error';
        }
    }
    public function index()
    {
        $klipings = Kliping::latest()->paginate(10);
        return view('klipings.index', compact('klipings'));
    }
    public function edit($id)
    {
        $kliping = Kliping::findOrFail($id);
        return view('klipings.form', [
            'kliping' => $kliping,
            'isEdit' => true
        ]);
    }

    public function update(Request $request, $id)
    {
        $kliping = Kliping::findOrFail($id);

        $request->validate([
            'nama_media'   => 'required|in:Koran Buleleng,Radar Bali,Bali Post,Tribun Bali,Nusa Bali',
            'media_type'   => 'required|in:online,offline',
            'kategori'     => 'required|in:Olahraga,Pendidikan,Pemerintahan,Kesehatan,Ekonomi',
            'sub_kategori' => 'nullable|string|max:255',
            'tanggal'      => 'required|date',
            'gambar'       => 'nullable|image|max:2048',
            'judul'        => 'required_if:media_type,online|nullable|string',
            'isi'          => 'nullable|string',
            'link'         => 'required_if:media_type,online|nullable|url',
            'klasifikasi'  => 'nullable|in:Positif,Netral,Negatif',
        ]);

        $data = $request->only([
            'nama_media', 'kategori', 'sub_kategori', 'judul', 'isi', 'link', 'tanggal'
        ]);
        $data['media_type'] = $request->input('media_type');

        // upload gambar baru kalau ada
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('kliping-images', 'public');
            $data['gambar_path'] = $path;
        }

        // === klasifikasi (manual atau otomatis) ===
        if ($request->filled('klasifikasi')) {
            // user pilih manual dari form
            $data['klasifikasi'] = $request->klasifikasi;
        } else {
            // otomatis pakai OCR atau isi teks
            if ($data['media_type'] === 'offline' && isset($data['gambar_path'])) {
                $ocrText = (new \thiagoalessio\TesseractOCR\TesseractOCR(
                    storage_path('app/public/'.$data['gambar_path'])
                ))->run();

                $data['ocr_text'] = $ocrText;
                $data['klasifikasi'] = ucfirst($this->analyzeSentiment($this->limitText($ocrText)));
            } elseif ($data['media_type'] === 'online') {
                $textToAnalyze = trim(($request->judul ?? '').' '.($request->isi ?? ''));
                $data['klasifikasi'] = ucfirst($this->analyzeSentiment($this->limitText($textToAnalyze)));
            }
        }

        // update database
        $kliping->update($data);

        // === Regenerasi PDF ===
        try {
            $base64Image = $kliping->gambar_path
                ? $this->imageToBase64($kliping->gambar_path)
                : null;

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('klipings.pdf', [
                'kliping' => $kliping->fresh(), // pastikan ambil data terbaru
                'base64Image' => $base64Image
            ]);

            $pdfPath = 'kliping-pdf/kliping_' . $kliping->id . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());
            $kliping->update(['pdf_path' => $pdfPath]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF baru: ' . $e->getMessage());
        }

        return redirect()->route('klipings.preview', $kliping->id)
            ->with('success', 'Kliping berhasil diperbarui dan PDF digenerate ulang.');
    }

}
