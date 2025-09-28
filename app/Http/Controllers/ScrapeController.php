<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ScrapePortalJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ScrapeController extends Controller
{
    public function showForm()
    {
        return view('scrapping.formScrapping');
    }

    // Handle form scraping dan dispatch job
    public function scrape(Request $request)
    {
        $request->validate([
            'pages'  => 'required|integer|min:1',
            'portal' => 'required|string|in:balipost,balipuspanews,koranbuleleng,fajarbali,wartabali'
        ]);

        $pages  = (int) $request->input('pages');
        $portal = trim(strtolower($request->input('portal')));

        // Generate batch_id unik
        $batchId = Str::uuid()->toString();

        // Dispatch job scraping
        ScrapePortalJob::dispatch($portal, $pages, $batchId);

        // Simpan batch_id dan portal ke session (opsional, tapi tetap boleh)
        session([
            'last_batch_id' => $batchId,
            'last_portal'   => $portal
        ]);

        // Kembalikan JSON untuk AJAX
        return response()->json([
            'success' => true,
            'batchId' => $batchId,
            'portal' => $portal
        ]);
    }

    // Tampilkan hasil scraping berdasarkan batch_id terbaru
    public function results()
    {
        $batchId = session('last_batch_id');

        if (!$batchId) {
            $results = collect(); // kosong jika belum scraping
        } else {
            $results = DB::table('scraping_results')
                        ->where('batch_id', $batchId)
                        ->orderByDesc('created_at')
                        ->get();
        }

        // Simpan hasil ke session agar bisa didownload
        session(['scraping_results' => $results]);

        return view('scrapping.result', [
            'results' => $results,
        ]);
    }

    // Download CSV hasil scraping
    public function downloadCsv(Request $request)
    {
        $results = session('scraping_results', []);

        if (empty($results)) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $csvFileName = 'scraping_results_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');
            // Header kolom
            fputcsv($file, ['Judul', 'Konten', 'Tanggal', 'Portal', 'URL'], ',', '"');

            foreach ($results as $row) {
                fputcsv($file, [
                    $row->title ?? '-',
                    preg_replace("/\r|\n/", " ", $row->content ?? '-'),
                    $row->tanggal ?? '-',
                    $row->portal ?? '-',
                    $row->url ?? '-'
                ], ',', '"');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Cek status apakah scraping sudah selesai
    public function checkStatus(Request $request)
    {
        $batchId = $request->input('batch_id');

        if (!$batchId) {
            return response()->json(['done' => false]);
        }

        $exists = DB::table('scraping_results')
                    ->where('batch_id', $batchId)
                    ->exists();

        return response()->json(['done' => $exists]);
    }

}
