<?php

namespace App\Http\Controllers\Scraping;

use App\Http\Requests\ScrapingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class LeboncoinScraperController
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
            'titre_annonce' => '[data-qa-id="adview_spotlight_description_container"] h1[data-qa-id="adview_title"]',
            'prix_annonce' => '[data-qa-id="adview_price"]',
            'caracteristique_accroche' => '[data-qa-id="adview_spotlight_description_container"] div[class="flex flex-wrap"] p[class="inline-flex w-full flex-wrap mb-md"]',
            // 'localisation' => '[class*="Localizationstyled__Title-sc"]',
            'description' => '[data-qa-id="adview_description_container"]',
            'tags' => '[data-test-id="criteria"]',
            // 'conso_energetique' => '[data-test*="diagnostics-preview-bar-energy"] div[class*="Previewstyled__PreviewTile-sc"] div[class*="Previewstyled__Grade-sc"]',
            // 'conso_gaz_emission' => '[data-test*="diagnostics-preview-bar-emission"] div[class*="Previewstyled__PreviewTile-sc"] div[class*="Previewstyled__Grade-sc"]',
            // 'type_chauffage' => '[class*="Diagnosticsstyled__TextWrapper-sc"]'
        ];

        $results = [];

        foreach ($selectors as $key => $selector) {
            $values = $crawler->filter($selector)->each(fn($node) => trim($node->text()));
            $results[$key] = count($values) > 0 ? $values : ['empty'];
        }

        return response()->json($results);
    }
}
