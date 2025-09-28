<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScrapingResult;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class KlipingController extends Controller
{
    public function form()
    {
        $portals = ScrapingResult::select('portal')->distinct()->pluck('portal');
        return view('kliping.form', compact('portals'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'portal' => 'required',
            'tanggal' => 'required|date',
        ]);

        // Set locale ke Bahasa Indonesia
        Carbon::setLocale('id');

        // Format tanggal agar sesuai dengan format di database
        $tanggal_formated = Carbon::parse($request->tanggal)->translatedFormat('j F Y');

        $berita = ScrapingResult::where('portal', $request->portal)
            ->where('tanggal', $tanggal_formated)
            ->first();

        if (!$berita) {
            return back()->with('error', 'Berita tidak ditemukan.');
        }

        $response = Http::post('http://127.0.0.1:5001/analyze', [
            'text' => $berita->content
        ]);

        $json = $response->json();
        $sentimen = $json['label'] ?? 'Tidak Terdeteksi';

        $pdf = Pdf::loadView('kliping.template', [
            'berita' => $berita,
            'sentimen' => $sentimen
        ]);

        return $pdf->download("kliping_{$request->tanggal}.pdf");
    }

}
