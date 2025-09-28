<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\Kliping;

class KlipingsApiController extends Controller
{
    public function index()
    {
        $klipings = Kliping::latest()->paginate(10);
        return response()->json($klipings);
    }

    public function show($id)
    {
        $kliping = Kliping::findOrFail($id);
        return response()->json($kliping);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_media'   => 'required|in:Koran Buleleng,Radar Bali,Bali Post,Tribun Bali,Nusa Bali',
            'media_type'   => 'required|in:online,offline',
            'kategori'     => 'required|in:Olahraga,Pendidikan,Pemerintahan,Kesehatan,Ekonomi',
            'sub_kategori' => 'nullable|string|max:255',
            'tanggal'      => 'required|date',
            'gambar'       => 'required|image|max:2048',
            'judul'        => 'required_if:media_type,online|nullable|string',
            'isi'          => 'nullable|string',
            'link'         => 'required_if:media_type,online|nullable|url',
            'klasifikasi'  => 'nullable|in:Positif,Netral,Negatif',
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

        // Klasifikasi manual atau otomatis
        if ($request->filled('klasifikasi')) {
            $data['klasifikasi'] = $request->input('klasifikasi');
        } else {
            if ($mediaType === 'offline') {
                $imagePath = storage_path('app/public/' . $data['gambar_path']);
                try {
                    $ocr = new TesseractOCR($imagePath);
                    $ocrText = $ocr->run();
                    $data['ocr_text'] = $ocrText;
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Gagal OCR: ' . $e->getMessage()], 500);
                }

                $limitedText = $this->limitText($ocrText);
                $data['klasifikasi'] = ucfirst($this->analyzeSentiment($limitedText));
            } else {
                $textToAnalyze = trim(($request->judul ?? '') . ' ' . ($request->isi ?? ''));
                $limitedText = $this->limitText($textToAnalyze);
                $data['klasifikasi'] = ucfirst($this->analyzeSentiment($limitedText));
            }
        }

        $kliping = Kliping::create($data);

        // Generate PDF (optional, bisa di skip kalau API fokus data aja)
        try {
            $base64Image = $kliping->gambar_path ? $this->imageToBase64($kliping->gambar_path) : null;

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('klipings.pdf', [
                'kliping' => $kliping,
                'base64Image' => $base64Image
            ]);

            $pdfPath = 'kliping-pdf/kliping_' . $kliping->id . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());
            $kliping->update(['pdf_path' => $pdfPath]);
        } catch (\Exception $e) {
            // Kalau error generate PDF, tetap lanjutkan simpan data
        }

        return response()->json([
            'message' => 'Kliping berhasil dibuat',
            'data' => $kliping,
        ], 201);
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

        // Upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('kliping-images', 'public');
            $data['gambar_path'] = $path;
        }

        if ($request->filled('klasifikasi')) {
            $data['klasifikasi'] = $request->input('klasifikasi');
        } else {
            if ($data['media_type'] === 'offline' && isset($data['gambar_path'])) {
                try {
                    $ocrText = (new TesseractOCR(storage_path('app/public/' . $data['gambar_path'])))->run();
                    $data['ocr_text'] = $ocrText;
                    $data['klasifikasi'] = ucfirst($this->analyzeSentiment($this->limitText($ocrText)));
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Gagal OCR: ' . $e->getMessage()], 500);
                }
            } elseif ($data['media_type'] === 'online') {
                $textToAnalyze = trim(($request->judul ?? '') . ' ' . ($request->isi ?? ''));
                $data['klasifikasi'] = ucfirst($this->analyzeSentiment($this->limitText($textToAnalyze)));
            }
        }

        $kliping->update($data);

        // Regenerate PDF (optional)
        try {
            $base64Image = $kliping->gambar_path ? $this->imageToBase64($kliping->gambar_path) : null;

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('klipings.pdf', [
                'kliping' => $kliping->fresh(),
                'base64Image' => $base64Image
            ]);

            $pdfPath = 'kliping-pdf/kliping_' . $kliping->id . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());
            $kliping->update(['pdf_path' => $pdfPath]);
        } catch (\Exception $e) {
            // Abaikan error generate PDF
        }

        return response()->json([
            'message' => 'Kliping berhasil diperbarui',
            'data' => $kliping,
        ]);
    }

    public function destroy($id)
    {
        $kliping = Kliping::findOrFail($id);
        $kliping->delete();

        return response()->json([
            'message' => 'Kliping berhasil dihapus',
        ]);
    }

    // Fungsi pembantu

    private function limitText($text)
    {
        $words = preg_split('/\s+/', trim($text));
        $limitedText = implode(' ', array_slice($words, 0, 374));
        return substr($limitedText, 0, 2000);
    }

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
     public function storeAndDownload(Request $request)
    {
        $request->validate([
            'nama_media'   => 'required|in:Koran Buleleng,Radar Bali,Bali Post,Tribun Bali,Nusa Bali',
            'media_type'   => 'required|in:online,offline',
            'kategori'     => 'required|in:Olahraga,Pendidikan,Pemerintahan,Kesehatan,Ekonomi',
            'sub_kategori' => 'nullable|string|max:255',
            'tanggal'      => 'required|date',
            'gambar'       => 'required|image|max:2048',
            'judul'        => 'required_if:media_type,online|nullable|string',
            'isi'          => 'nullable|string',
            'link'         => 'required_if:media_type,online|nullable|url',
            'klasifikasi'  => 'nullable|in:Positif,Netral,Negatif',
        ]);

        // Proses sama seperti fungsi store untuk simpan data
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

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('kliping-images', 'public');
            $data['gambar_path'] = $path;
        }

        if ($request->filled('klasifikasi')) {
            $data['klasifikasi'] = $request->input('klasifikasi');
        } else {
            if ($mediaType === 'offline') {
                $imagePath = storage_path('app/public/' . $data['gambar_path']);
                try {
                    $ocr = new TesseractOCR($imagePath);
                    $ocrText = $ocr->run();
                    $data['ocr_text'] = $ocrText;
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Gagal OCR: ' . $e->getMessage()], 500);
                }

                $limitedText = $this->limitText($ocrText);
                $data['klasifikasi'] = ucfirst($this->analyzeSentiment($limitedText));
            } else {
                $textToAnalyze = trim(($request->judul ?? '') . ' ' . ($request->isi ?? ''));
                $limitedText = $this->limitText($textToAnalyze);
                $data['klasifikasi'] = ucfirst($this->analyzeSentiment($limitedText));
            }
        }

        $kliping = Kliping::create($data);

        // Generate PDF
        $base64Image = $kliping->gambar_path ? $this->imageToBase64($kliping->gambar_path) : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('klipings.pdf', [
            'kliping' => $kliping,
            'base64Image' => $base64Image
        ]);

        // Kirim PDF langsung sebagai response, agar browser bisa preview/download
        return $pdf->download('kliping_' . $kliping->id . '.pdf');
    }

}
