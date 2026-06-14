<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BeritaEksternalController extends Controller
{
    /**
     * Get external news from various sources
     */
    public function index(Request $request)
    {
        try {
            $source = $request->get('source', 'antara');
            $limit = $request->get('limit', 10);

            $news = [];

            switch ($source) {
                case 'antara':
                    $news = $this->scrapeAntara($limit);
                    break;
                case 'tempo':
                    $news = $this->scrapeTempo($limit);
                    break;
                case 'all':
                    $news = $this->getAllNews($limit);
                    break;
                default:
                    $news = $this->scrapeAntara($limit);
            }

            return response()->json([
                'success' => true,
                'data' => $news,
                'source' => 'live'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil berita eksternal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get combined news from all sources (external only)
     */
    public function combined(Request $request)
    {
        try {
            $limit = $request->get('limit', 20);

            $allNews = [];

            // Get news from different sources
            $sources = ['antara', 'tempo'];
            foreach ($sources as $source) {
                try {
                    $news = $this->getNewsFromSource($source, 5);
                    $allNews = array_merge($allNews, $news);
                } catch (\Exception $e) {
                    // Continue with other sources if one fails
                    continue;
                }
            }

            // Sort by date and limit
            usort($allNews, function($a, $b) {
                return strtotime($b['published_at']) - strtotime($a['published_at']);
            });

            $allNews = array_slice($allNews, 0, $limit);

            return response()->json([
                'success' => true,
                'data' => $allNews,
                'source' => 'combined'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil berita gabungan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get combined news from internal and external sources
     */
    public function internalExternalCombined(Request $request)
    {
        try {
            $internalLimit = $request->get('internal_limit', 5);
            $externalLimit = $request->get('external_limit', 5);
            $externalSource = $request->get('source', 'antara');

            $allNews = [];

            // Get internal berita
            try {
                $internalBeritas = \App\Models\Berita::published()
                    ->with('author')
                    ->orderBy('published_at', 'desc')
                    ->limit($internalLimit)
                    ->get()
                    ->map(function($berita) {
                        return [
                            'id' => $berita->id,
                            'title' => $berita->judul,
                            'slug' => $berita->slug,
                            'description' => $berita->konten,
                            'link' => route('berita.show', $berita->slug),
                            'image' => $berita->image_url,
                            'published_at' => $berita->published_at ? $berita->published_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                            'source' => 'Desa Cibatu',
                            'category' => $berita->kategori,
                            'author' => $berita->author ? $berita->author->name : 'Admin Desa',
                            'is_external' => false
                        ];
                    });

                $allNews = array_merge($allNews, $internalBeritas->toArray());
            } catch (\Exception $e) {
                // Continue if internal berita fails
            }

            // Get external berita
            try {
                $externalNews = $this->getNewsFromSource($externalSource, $externalLimit);
                $allNews = array_merge($allNews, $externalNews);
            } catch (\Exception $e) {
                // Continue if external berita fails
            }

            // Sort by date and limit
            usort($allNews, function($a, $b) {
                return strtotime($b['published_at']) - strtotime($a['published_at']);
            });

            return response()->json([
                'success' => true,
                'data' => $allNews,
                'source' => 'internal_external_combined'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil berita gabungan internal dan eksternal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Scrape Antara News RSS Feed
     */
    private function scrapeAntara($limit = 10)
    {
        try {
            $response = Http::timeout(15)->get('https://www.antaranews.com/rss/terkini.xml');

            if (!$response->successful()) {
                return $this->getFallbackNews();
            }

            $xml = simplexml_load_string($response->body());
            if (!$xml) {
                return $this->getFallbackNews();
            }

            $news = [];
            $count = 0;

            foreach ($xml->channel->item as $item) {
                if ($count >= $limit) break;

                $description = (string) $item->description;
                $imageUrl = $this->extractImageFromDescription($description);
                $cleanDescription = $this->cleanDescription($description);

                $news[] = [
                    'id' => 'antara_' . $count,
                    'title' => (string) $item->title,
                    'description' => $cleanDescription,
                    'link' => (string) $item->link,
                    'url' => (string) $item->link,
                    'image' => $imageUrl,
                    'published_at' => date('Y-m-d H:i:s', strtotime($item->pubDate)),
                    'source' => 'Antara',
                    'is_external' => true,
                    'category' => 'Nasional',
                    'author' => ['name' => 'Antara News']
                ];

                $count++;
            }

            return $news;

        } catch (\Exception $e) {
            return $this->getFallbackNews();
        }
    }

    /**
     * Get news from specific source
     */
    private function getNewsFromSource($source, $limit)
    {
        switch ($source) {
            case 'antara':
                return $this->scrapeAntara($limit);
            case 'tempo':
                return $this->scrapeTempo($limit);
            default:
                return $this->scrapeAntara($limit);
        }
    }

    /**
     * Get all news from different sources
     */
    private function getAllNews($limit)
    {
        $allNews = [];
        $sources = ['antara', 'tempo'];

        foreach ($sources as $source) {
            try {
                $news = $this->getNewsFromSource($source, 5);
                $allNews = array_merge($allNews, $news);
            } catch (\Exception $e) {
                continue;
            }
        }

        // Sort by date and limit
        usort($allNews, function($a, $b) {
            return strtotime($b['published_at']) - strtotime($a['published_at']);
        });

        return array_slice($allNews, 0, $limit);
    }

    /**
     * Extract image URL from description
     */
    private function extractImageFromDescription($description)
    {
        // Try to extract image from description
        if (preg_match('/<img[^>]+src="([^"]+)"/', $description, $matches)) {
            return $matches[1];
        }

        // Return default image if no image found
        return null;
    }

    /**
     * Clean HTML from description
     */
    private function cleanDescription($description)
    {
        if (!$description) return '';
        // Remove HTML tags and get plain text
        return strip_tags($description);
    }

    /**
     * Scrape Tempo RSS Feed
     */
    private function scrapeTempo($limit = 10)
    {
        try {
            // Coba beberapa URL RSS Tempo yang berbeda
            $rssUrls = [
                'https://rss.tempo.co/nasional',
                'https://www.tempo.co/rss/terkini',
                'https://rss.tempo.co/terkini'
            ];

            $response = null;
            $xml = null;

            foreach ($rssUrls as $url) {
                try {
                    $response = Http::timeout(15)->get($url);
                    if ($response->successful()) {
                        $xml = simplexml_load_string($response->body());
                        if ($xml) {
                            break; // Berhasil, keluar dari loop
                        }
                    }
                } catch (\Exception $e) {
                    continue; // Coba URL berikutnya
                }
            }

            if (!$xml) {
                return $this->getFallbackNews();
            }

            $news = [];
            $count = 0;

            foreach ($xml->channel->item as $item) {
                if ($count >= $limit) break;

                $description = (string) $item->description;
                $cleanDescription = $this->cleanDescription($description);

                // Tempo uses <img> tag directly, not in description
                $imageUrl = '';
                if (isset($item->img)) {
                    $imageUrl = (string) $item->img;
                } else {
                    // Fallback to extract from description if no direct img tag
                    $imageUrl = $this->extractImageFromDescription($description);
                }

                $news[] = [
                    'id' => 'tempo_' . $count,
                    'title' => (string) $item->title,
                    'description' => $cleanDescription,
                    'link' => (string) $item->link,
                    'url' => (string) $item->link,
                    'image' => $imageUrl,
                    'published_at' => date('Y-m-d H:i:s', strtotime($item->pubDate)),
                    'source' => 'Tempo',
                    'is_external' => true,
                    'category' => 'Nasional',
                    'author' => ['name' => 'Tempo.co']
                ];

                $count++;
            }

            return $news;

        } catch (\Exception $e) {
            return $this->getFallbackNews();
        }
    }

    /**
     * Get fallback news when scraping fails
     */
    private function getFallbackNews()
    {
        return [
            [
                'id' => 'fallback_1',
                'title' => 'Berita Tempo Sedang Dimuat',
                'description' => 'Sistem sedang memuat berita terbaru dari Tempo.co. Silakan coba lagi dalam beberapa saat atau gunakan sumber berita Antara News yang lebih stabil.',
                'link' => 'https://www.tempo.co',
                'image' => null,
                'published_at' => now()->format('Y-m-d H:i:s'),
                'source' => 'Tempo',
                'is_external' => true,
                'category' => 'Nasional',
                'author' => ['name' => 'Tempo.co']
            ],
            [
                'id' => 'fallback_2',
                'title' => 'Tips Menggunakan Fitur Berita',
                'description' => 'Jika berita Tempo tidak muncul, coba refresh halaman atau pilih sumber berita Antara News. RSS feed Tempo kadang tidak dapat diakses.',
                'link' => '#',
                'image' => null,
                'published_at' => now()->subMinutes(5)->format('Y-m-d H:i:s'),
                'source' => 'Tips',
                'is_external' => false,
                'category' => 'Tips',
                'author' => ['name' => 'Sistem']
            ]
        ];
    }
}

