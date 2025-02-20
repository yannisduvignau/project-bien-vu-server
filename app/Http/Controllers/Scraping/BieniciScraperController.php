<?php

namespace App\Http\Controllers\Scraping;

use App\Http\Requests\ScrapingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class BieniciScraperController
{
    public function scrape(ScrapingRequest $request)
    {
        $validated = $request->validated();
        $url = $validated['url'];

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            'Accept-Language' => 'fr-FR,fr;q=0.9',
        ])->get($url);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch the URL'], 500);
        }

        $html = $response->body();
        $crawler = new Crawler($html);

        $selectors = [
            'titre_annonce' => 'h1',
            'prix_annonce' => 'span.ad-price__the-price',
            'localisation' => 'span.fullAddress',
            'description' => 'section.description',
            'tags' => 'div.allDetails',
            'conso_energetique' => 'div.dpe-line__classification',
            'conso_gaz_emission' => 'div.ges-line__classification',
        ];

        $results = [];

        foreach ($selectors as $key => $selector) {
            $values = $crawler->filter($selector)->each(fn($node) => trim($node->text()));
            $results[$key] = count($values) > 0 ? $values : ['empty'];
        }

        return response()->json($results);
    }
}
