<?php

namespace App\Http\Controllers\Scraping;

use App\Http\Requests\ScrapingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class SelogerScraperController
{
    public function scrape(ScrapingRequest $request)
    {
        $validated = $request->validated();
        $url = $validated['url'];

        $response = Http::get($url);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch the URL'], 500);
        }

        $html = $response->body();
        $crawler = new Crawler($html);

        $selectors = [
            'titre_annonce' => '[class*="Summarystyled__Title-sc"]',
            // 'prix_annonce' => '[class*="Summarystyled__PriceText-sc"]',
            'caracteristique_accroche' => '[class*="Summarystyled__Title-sc"]',
            'localisation' => '[class*="Localizationstyled__Title-sc"]',
            'description' => '[class*="TitledDescription__TitledDescriptionContent-sc"]',
            'tags' => '[class*="Summarystyled__TagsWrapper-sc"]',
            'conso_energetique' => '[data-test*="diagnostics-preview-bar-energy"] div[class*="Previewstyled__PreviewTile-sc"] div[class*="Previewstyled__Grade-sc"]',
            'conso_gaz_emission' => '[data-test*="diagnostics-preview-bar-emission"] div[class*="Previewstyled__PreviewTile-sc"] div[class*="Previewstyled__Grade-sc"]',
            'type_chauffage' => '[class*="Diagnosticsstyled__TextWrapper-sc"]'
        ];

        $results = [];

        foreach ($selectors as $key => $selector) {
            $values = $crawler->filter($selector)->each(fn($node) => trim($node->text()));
            $results[$key] = count($values) > 0 ? $values : ['empty'];
        }

        return response()->json($results);
    }
}
