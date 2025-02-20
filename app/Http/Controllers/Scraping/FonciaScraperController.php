<?php

namespace App\Http\Controllers\Scraping;

use App\Http\Requests\ScrapingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class FonciaScraperController
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
            'titre_annonce' => 'h1.section-title',
            'prix_annonce' => 'p.price-value',
            'caracteristique_accroche' => 'p.feature-icons',
            'localisation' => 'p.location',
            'description' => 'p.section-description',
            'tags' => 'div.features',
            'conso_energetique' => 'div.dpe-diagnostic:first-of-type img',
            'conso_gaz_emission' => 'div.dpe-diagnostic:nth-of-type(2) img',
        ];

        $results = [];

        foreach ($selectors as $key => $selector) {
            if (str_contains($selector, 'img')) {
                $values = $crawler->filter($selector)->each(fn($node) => $node->attr('alt') ?? 'empty');
            } else {
                $values = $crawler->filter($selector)->each(fn($node) => trim($node->text()));
            }
            $results[$key] = count($values) > 0 ? $values : ['empty'];
        }

        return response()->json($results);
    }
}
