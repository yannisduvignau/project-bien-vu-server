<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ScrapingAction
{
    public function execute(string $url)
    {
        $allowedDomains = [
            'bienici.com',
            'leboncoin.fr',
            'foncia.com',
            'logic-immo.com',
            'seloger.com',
        ];

        // Extraire le domaine de l'URL
        $parsedUrl = parse_url($url, PHP_URL_HOST);
        $domain = preg_replace('/^www\./', '', $parsedUrl);

        // Vérifier si le domaine est supporté
        if (!in_array($domain, $allowedDomains)) {
            return response()->json(['error' => "Le domaine $domain n'est pas supporté."], 400);
        }

        return match ($domain) {
            'bienici.com' => response()->json($this->scrapBienici($url)),
            'leboncoin.fr' => response()->json($this->scrapLeboncoin($url)),
            'foncia.com' => response()->json($this->scrapFoncia($url)),
            'logic-immo.com' => response()->json($this->scrapLogicimmo($url)),
            'seloger.com' => response()->json($this->scrapSeloger($url)),
            default => response()->json(['error' => 'Domaine non supporté'], 400),
        };
    }

    private function scrapBienici(string $url)
    {
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
            $results[$key] = count($values) > 0 ? $values : null;
        }

        return $results;
    }

    private function scrapLeboncoin(string $url)
    {
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

    private function scrapFoncia(string $url)
    {
        return ['error' => 'Scraping pour Foncia non implémenté.'];
    }

    private function scrapLogicimmo(string $url)
    {
        return ['error' => 'Scraping pour Logic-Immo non implémenté.'];
    }

    private function scrapSeloger(string $url)
    {
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
