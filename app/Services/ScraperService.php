<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class ScraperService
{
    // gunakan property processed untuk job tunggal (agar dedupe per job)
    private array $processedTitles = [];
    private array $processedUrls = [];
    private array $processedHashes = [];

    public function scrape(string $portal, int $pages = 1): array
    {
        return match($portal) {
            'balipost' => $this->scrapeBalipost($pages),
            'balipuspanews' => $this->scrapeBalipuspanews($pages),
            'koranbuleleng' => $this->scrapeKoranBuleleng($pages),
            'fajarbali' => $this->scrapeFajarBali($pages),
            'wartabali' => $this->scrapeWartabali($pages),
            // tambahkan portal lain => fungsi private masing2
            default => [],
        };
    }

 /**
     * Scraper Balipost
     */
    private function scrapeBalipost(int $maxPages): array
    {
        $results = [];
        $client = new Client([
            'headers' => ['User-Agent' => 'Mozilla/5.0'],
            'timeout' => 60,
            'verify' => false,
        ]);

        for ($page = 1; $page <= $maxPages; $page++) {
            $url = "https://www.balipost.com/bali/buleleng/page/{$page}";
            \Log::info("➡️ Scraping Balipost halaman {$page}: {$url}");

            try {
                $html = (string) $client->get($url)->getBody();
                $crawler = new Crawler($html);

                $articles = $crawler->filter('div.td-module-thumb a')->each(function (Crawler $node) {
                    return [
                        'title' => trim($node->attr('title') ?? ''),
                        'url'   => trim($node->attr('href') ?? ''),
                    ];
                });

                foreach ($articles as $article) {
                    if (empty($article['title']) || empty($article['url'])) {
                        continue;
                    }

                    if (in_array($article['title'], $this->processedTitles)) {
                        continue;
                    }
                    $this->processedTitles[] = $article['title'];

                    if (in_array($article['url'], $this->processedUrls)) {
                        continue;
                    }
                    $this->processedUrls[] = $article['url'];

                    $content = $this->getArticleContentBalipost($client, $article['url']);
                    if (empty($content)) {
                        continue;
                    }

                    $hash = hash('sha256', $content);
                    if (in_array($hash, $this->processedHashes)) {
                        continue;
                    }
                    $this->processedHashes[] = $hash;

                    $results[] = [
                        'title'    => $article['title'],
                        'content'  => $content,
                        'tanggal'  => $this->getDateBalipost($client, $article['url']),
                        'url'      => $article['url'],
                    ];
                }
            } catch (\Throwable $e) {
                \Log::warning("❌ Gagal scraping Balipost halaman {$page}: " . $e->getMessage());
                continue;
            }

            sleep(1);
        }

        return $results;
    }

    private function getArticleContentBalipost(Client $client, string $url): string
    {
        try {
            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            $contentNodes = $crawler->filter('.td-post-content p');
            if ($contentNodes->count() === 0) {
                $contentNodes = $crawler->filter('article p');
            }

            $paragraphs = $contentNodes->each(fn(Crawler $node) => trim($node->text()));
            return implode("\n\n", $paragraphs);
        } catch (\Throwable $e) {
            \Log::warning("❌ Gagal ambil konten Balipost {$url}: {$e->getMessage()}");
            return '';
        }
    }

    private function getDateBalipost(Client $client, string $url): string
    {
        try {
            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            $tanggalNode = $crawler->filter('.td-post-date');
            if ($tanggalNode->count()) {
                $tanggalRaw = trim($tanggalNode->text());

                $parts = explode(',', $tanggalRaw);
                if (count($parts) > 1) {
                    $tanggalClean = trim($parts[1]);
                    $tanggalParts = explode('|', $tanggalClean);
                    return trim($tanggalParts[0]);
                }
                return $tanggalRaw;
            }
        } catch (\Throwable $e) {
            return '-';
        }

        return '-';
    }

    private function scrapeBalipuspanews(int $maxPages): array
    {
        $results = [];
        $client = new Client([
            'headers' => ['User-Agent' => 'Mozilla/5.0'],
            'timeout' => 60,
            'verify' => false,
        ]);

        for ($page = 1; $page <= $maxPages; $page++) {
            $url = $page === 1 
                ? "https://www.balipuspanews.com/category/buleleng" 
                : "https://www.balipuspanews.com/category/buleleng/page/{$page}";

            \Log::info("➡️ Scraping Balipuspanews halaman {$page}: {$url}");

            try {
                $html = (string) $client->get($url)->getBody();
                $crawler = new Crawler($html);

                // Ambil daftar artikel
                $articles = $crawler->filter('h3.entry-title.td-module-title a')->each(function (Crawler $node) {
                    return [
                        'title' => trim($node->text('') ?? ''),
                        'url'   => trim($node->attr('href') ?? ''),
                    ];
                });

                foreach ($articles as $article) {
                    if (empty($article['title']) || empty($article['url'])) {
                        continue;
                    }

                    if (in_array($article['title'], $this->processedTitles)) {
                        continue;
                    }
                    $this->processedTitles[] = $article['title'];

                    if (in_array($article['url'], $this->processedUrls)) {
                        continue;
                    }
                    $this->processedUrls[] = $article['url'];

                    $content = $this->getArticleContentBalipuspanews($client, $article['url']);
                    if (empty($content)) {
                        continue;
                    }

                    $hash = hash('sha256', $content);
                    if (in_array($hash, $this->processedHashes)) {
                        continue;
                    }
                    $this->processedHashes[] = $hash;

                    $results[] = [
                        'title'   => $article['title'],
                        'content' => $content,
                        'tanggal' => $this->getDateBalipuspanews($client, $article['url']),
                        'url'     => $article['url'],
                    ];
                }
            } catch (\Throwable $e) {
                \Log::warning("❌ Gagal scraping Balipuspanews halaman {$page}: " . $e->getMessage());
                continue;
            }

            sleep(1);
        }

        return $results;
    }

    private function getArticleContentBalipuspanews(Client $client, string $url): string
    {
        try {
            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            // Isi artikel
            $contentNodes = $crawler->filter('div.tdb-block-inner.td-fix-index p');

            $paragraphs = $contentNodes->each(fn(Crawler $node) => trim($node->text()));
            return implode("\n\n", $paragraphs);
        } catch (\Throwable $e) {
            \Log::warning("❌ Gagal ambil konten Balipuspanews {$url}: {$e->getMessage()}");
            return '';
        }
    }

    private function getDateBalipuspanews(Client $client, string $url): string
    {
        try {
            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            $tanggalNode = $crawler->filter('time.entry-date');
            if ($tanggalNode->count()) {
                $tanggalRaw = trim($tanggalNode->text());

                $parts = explode(',', $tanggalRaw);
                if (count($parts) > 1) {
                    $tanggalClean = trim($parts[1]);
                    $tanggalParts = explode('|', $tanggalClean);
                    return trim($tanggalParts[0]);
                }
                return $tanggalRaw;
            }
        } catch (\Throwable $e) {
            return '-';
        }

        return '-';
    }

    /**
     * Scraper Koran Buleleng
     */
    private function scrapeKoranBuleleng(int $maxPages): array
    {
        $results = [];
        $client = new Client([
            'headers' => [
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9,id;q=0.8',
                'Referer'         => 'https://www.google.com/',
                'Connection'      => 'keep-alive',
            ],
            'timeout' => 60,
            'verify' => false,
        ]);

        for ($page = 1; $page <= $maxPages; $page++) {
            $url = $page === 1
                ? "https://koranbuleleng.com/category/news/"
                : "https://koranbuleleng.com/category/news/page/{$page}";

            \Log::info("➡️ Scraping Koran Buleleng halaman {$page}: {$url}");

            try {
                $html = (string) $client->get($url)->getBody();
                $crawler = new Crawler($html);

                $articles = $crawler->filter('h3.entry-title.td-module-title a')->each(function (Crawler $node) {
                    return [
                        'title' => trim($node->text() ?? ''),
                        'url'   => trim($node->attr('href') ?? ''),
                    ];
                });

                foreach ($articles as $article) {
                    if (empty($article['title']) || empty($article['url'])) {
                        continue;
                    }

                    if (in_array($article['title'], $this->processedTitles)) {
                        continue;
                    }
                    $this->processedTitles[] = $article['title'];

                    if (in_array($article['url'], $this->processedUrls)) {
                        continue;
                    }
                    $this->processedUrls[] = $article['url'];

                    $content = $this->getArticleContentKoranBuleleng($client, $article['url']);
                    if (empty($content)) {
                        continue;
                    }

                    $hash = hash('sha256', $content);
                    if (in_array($hash, $this->processedHashes)) {
                        continue;
                    }
                    $this->processedHashes[] = $hash;

                    $results[] = [
                        'title'   => $article['title'],
                        'content' => $content,
                        'tanggal' => $this->getDateKoranBuleleng($client, $article['url']),
                        'url'     => $article['url'],
                    ];
                }
            } catch (\Throwable $e) {
                \Log::warning("❌ Gagal scraping Koran Buleleng halaman {$page}: " . $e->getMessage());
                continue;
            }

            sleep(1);
        }

        return $results;
    }

    private function getArticleContentKoranBuleleng(Client $client, string $url): string
    {
        try {
            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            $contentNodes = $crawler->filter('div.tdb-block-inner.td-fix-index p');
            $paragraphs = $contentNodes->each(fn(Crawler $node) => trim($node->text()));

            return implode("\n\n", $paragraphs);
        } catch (\Throwable $e) {
            \Log::warning("❌ Gagal ambil konten Koran Buleleng {$url}: {$e->getMessage()}");
            return '';
        }
    }

    private function getDateKoranBuleleng(Client $client, string $url): string
    {
        try {
            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            $tanggalNode = $crawler->filter('time.entry-date');
            if ($tanggalNode->count()) {
                $tanggalRaw = trim($tanggalNode->text());

                // Gunakan Carbon untuk parsing dan format ulang
                \Carbon\Carbon::setLocale('id');
                return \Carbon\Carbon::parse($tanggalRaw)->translatedFormat('j F Y');
            }
        } catch (\Throwable $e) {
            \Log::warning("❌ Gagal ambil tanggal Koran Buleleng {$url}: {$e->getMessage()}");
            return '-';
        }

        return '-';
    }
    
/**
 * Scraper Fajar Bali
 */
    private function scrapeFajarbali(int $maxPages): array
    {
        $results = [];
        $client = new Client([
            'headers' => ['User-Agent' => 'Mozilla/5.0'],
            'timeout' => 60,
            'verify' => false,
        ]);

        for ($page = 1; $page <= $maxPages; $page++) {
            $url = $page === 1
                ? "https://fajarbali.com/category/berita/daerah/buleleng-daerah/"
                : "https://fajarbali.com/category/berita/daerah/buleleng-daerah/page/{$page}/";

            \Log::info("➡️ Scraping Fajar Bali halaman {$page}: {$url}");

            try {
                $html = (string) $client->get($url)->getBody();
                $crawler = new Crawler($html);

                // Ambil daftar artikel
                $articles = $crawler->filter('h2.entry-title.ast-blog-single-element a')->each(function (Crawler $node) {
                    return [
                        'title' => trim($node->text('') ?? ''),
                        'url'   => trim($node->attr('href') ?? ''),
                    ];
                });

                foreach ($articles as $article) {
                    if (empty($article['title']) || empty($article['url'])) {
                        continue;
                    }

                    if (in_array($article['title'], $this->processedTitles)) {
                        continue;
                    }
                    $this->processedTitles[] = $article['title'];

                    if (in_array($article['url'], $this->processedUrls)) {
                        continue;
                    }
                    $this->processedUrls[] = $article['url'];

                    $content = $this->getArticleContentFajarbali($client, $article['url']);
                    if (empty($content)) {
                        continue;
                    }

                    $hash = hash('sha256', $content);
                    if (in_array($hash, $this->processedHashes)) {
                        continue;
                    }
                    $this->processedHashes[] = $hash;

                    $results[] = [
                        'title'   => $article['title'],
                        'content' => $content,
                        'tanggal' => $this->getDateFajarbali($client, $article['url']),
                        'url'     => $article['url'],
                    ];
                }
            } catch (\Throwable $e) {
                \Log::warning("❌ Gagal scraping Fajar Bali halaman {$page}: " . $e->getMessage());
                continue;
            }

            sleep(1);
        }

        return $results;
    }

    private function getArticleContentFajarbali(Client $client, string $url): string
    {
        try {
            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            // Ambil isi artikel
            $contentNodes = $crawler->filter('div.elementor-widget-container p');

            $paragraphs = $contentNodes->each(fn(Crawler $node) => trim($node->text()));
            return implode("\n\n", $paragraphs);
        } catch (\Throwable $e) {
            \Log::warning("❌ Gagal ambil konten Fajar Bali {$url}: {$e->getMessage()}");
            return '';
        }
    }
        
    private function formatTanggalFajarbali(string $rawDate): string
    {
        try {
            // Normalisasi tanggal biar tidak ada spasi aneh
            $rawDate = trim($rawDate);

            // Misal "2025/09/07" → ubah jadi Carbon
            $date = Carbon::parse($rawDate);

            // Set locale ke Indonesia
            App::setLocale('id');
            Carbon::setLocale('id');

            // Format ke "7 September 2025"
            return $date->translatedFormat('j F Y');
        } catch (\Throwable $e) {
            \Log::warning("❌ Gagal format tanggal Fajar Bali: {$rawDate}");
            return $rawDate;
        }
    }

    private function getDateFajarbali(Client $client, string $url): string
    {
        try {
            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            $tanggalNode = $crawler->filter('span.elementor-icon-list-text.elementor-post-info__item.elementor-post-info__item--type-date');
            if ($tanggalNode->count()) {
                $raw = trim($tanggalNode->text());
                return $this->formatTanggalFajarbali($raw);
            }
        } catch (\Throwable $e) {
            return '-';
        }

        return '-';
    }

    /**
     * Scraper WartaBaliOnline
     */
    private function scrapeWartabali(int $maxPages): array
    {
        $results = [];
        $client = new Client([
            'headers' => ['User-Agent' => 'Mozilla/5.0'],
            'timeout' => 60,
            'verify' => false,
        ]);

        for ($page = 1; $page <= $maxPages; $page++) {
            $url = $page === 1
                ? "https://wartabalionline.com/category/daerah/buleleng/"
                : "https://wartabalionline.com/category/daerah/buleleng/page/{$page}/";

            \Log::info("➡️ Scraping WartaBaliOnline halaman {$page}: {$url}");

            try {
                $html = (string) $client->get($url)->getBody();
                $crawler = new Crawler($html);

                // ambil daftar artikel
                $articles = $crawler->filter('h2.post-title a')->each(function (Crawler $node) {
                    return [
                        'title' => trim($node->text() ?? ''),
                        'url'   => trim($node->attr('href') ?? ''),
                    ];
                });

                \Log::info("📌 Artikel ditemukan: " . count($articles));

                foreach ($articles as $article) {
                    if (empty($article['title']) || empty($article['url'])) {
                        continue;
                    }

                    if (in_array($article['title'], $this->processedTitles)) {
                        continue;
                    }
                    $this->processedTitles[] = $article['title'];

                    if (in_array($article['url'], $this->processedUrls)) {
                        continue;
                    }
                    $this->processedUrls[] = $article['url'];

                    $content = $this->getArticleContentWartabali($client, $article['url']);
                    if (empty($content)) {
                        continue;
                    }

                    $hash = hash('sha256', $content);
                    if (in_array($hash, $this->processedHashes)) {
                        continue;
                    }
                    $this->processedHashes[] = $hash;

                    $results[] = [
                        'title'    => $article['title'],
                        'content'  => $content,
                        'tanggal'  => $this->getDateWartabali($client, $article['url']),
                        'url'      => $article['url'],
                    ];
                }
            } catch (\Throwable $e) {
                \Log::warning("❌ Gagal scraping WartaBaliOnline halaman {$page}: " . $e->getMessage());
                continue;
            }

            sleep(1);
        }

        return $results;
    }

    private function getArticleContentWartabali(Client $client, string $url): string
    {
        try {
            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            $contentNodes = $crawler->filter('div.entry-content.entry.clearfix p');
            $paragraphs = $contentNodes->each(fn(Crawler $node) => trim($node->text()));

            return implode("\n\n", $paragraphs);
        } catch (\Throwable $e) {
            \Log::warning("❌ Gagal ambil konten WartaBaliOnline {$url}: {$e->getMessage()}");
            return '';
        }
    }

    private function getDateWartabali(Client $client, string $url): string
    {
        try {
            $html = (string) $client->get($url)->getBody();
            $crawler = new Crawler($html);

            $tanggalNode = $crawler->filter('span.date.meta-item.tie-icon');
            if ($tanggalNode->count()) {
                $tanggalRaw = trim($tanggalNode->text());
                // "Jumat, 5 September 2025 | 21:49 WITA"

                $tanggalClean = preg_replace('/\|.*/', '', $tanggalRaw); // hapus jam & WITA
                $tanggalClean = preg_replace(
                    '/^(Senin|Selasa|Rabu|Kamis|Jumat|Sabtu|Minggu),\s*/',
                    '',
                    $tanggalClean
                ); // hapus nama hari
                $tanggalClean = trim($tanggalClean);

                try {
                    $date = \Carbon\Carbon::parse($tanggalClean);
                    return $date->translatedFormat('j F Y'); // jadi "5 September 2025"
                } catch (\Exception $e) {
                    \Log::warning("⚠️ Gagal parse tanggal WartaBali: {$tanggalClean}");
                    return $tanggalClean;
                }
            }
        } catch (\Throwable $e) {
            \Log::warning("❌ Gagal ambil tanggal WartaBali {$url}: {$e->getMessage()}");
            return '-';
        }

        return '-';
    }
    // --- tambahkan fungsi private lain: getArticleContentBalipost, getDateBalipost, scrapeWartabali, dll ---
}
