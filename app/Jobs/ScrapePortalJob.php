<?php

namespace App\Jobs;

use App\Models\ScrapingResult;
use App\Services\ScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Log;

class ScrapePortalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $portal;
    public int $pages;
    public string $batchId;

    public $tries = 3;
    public $backoff = [30, 60];

    public function __construct(string $portal, int $pages = 1, string $batchId)
    {
        $this->portal = $portal;
        $this->pages = $pages;
        $this->batchId = $batchId;
    }

    public function handle(ScraperService $scraper)
    {
        Log::info("Job: Mulai scraping {$this->portal} untuk {$this->pages} halaman dengan batch_id {$this->batchId}");

        $results = $scraper->scrape($this->portal, $this->pages);

        foreach ($results as $item) {
            if (empty($item['url']) || empty($item['title'])) {
                continue;
            }

            $content = $item['content'] ?? '';
            $hash = hash('sha256', $content);

            try {
                ScrapingResult::updateOrCreate(
                    ['url' => $item['url']],
                    [
                        'portal'   => $this->portal,
                        'title'    => $item['title'],
                        'content'  => $content,
                        'tanggal'  => $item['tanggal'] ?? null,
                        'hash'     => $hash,
                        'batch_id' => $this->batchId,
                    ]
                );
            } catch (Throwable $e) {
                Log::warning("Gagal simpan hasil: {$e->getMessage()}");
            }
        }

        Log::info("Job: Selesai scraping {$this->portal} dengan batch_id {$this->batchId}");
    }

    public function failed(Throwable $exception)
    {
        Log::error("Job scraping {$this->portal} gagal: {$exception->getMessage()}");
    }
}
