<?php

namespace App\Http\Controllers\Scraping;

use App\Http\Requests\ScrapingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class LogicimmoScraperController
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
            'titre_annonce' => '[data-testid="aviv.CDP.Sections.Hardfacts"] div[class="css-74uxa4"] div[class="css-8g8ihq"] span[class="css-1nxshv1"]',
            'prix_annonce' => '[data-testid="aviv.CDP.Sections.Hardfacts.Price.Value"]',
            'caracteristique_accroche' => '[data-testid="aviv.CDP.Sections.Hardfacts"] div:nth-of-type(2)',
            'localisation' => '[data-testid="aviv.CDP.Sections.Location.Address"]',
            'description' => '[data-testid="aviv.CDP.Sections.Description.MainDescription"]',
            'tags' => '[data-testid="aviv.CDP.Sections.Features.Preview"]',
            'conso_energetique' => 'div[class="css-1rr4qq7"]:first-of-type [data-testid="aviv.CDP.Sections.Energy.Preview.EfficiencyClass"]',
            'conso_gaz_emission' => 'div[class="css-1rr4qq7"]:nth-of-type(2) [data-testid="aviv.CDP.Sections.Energy.Preview.EfficiencyClass"]',
            'type_chauffage' => '[data-testid="aviv.CDP.Sections.Energy.Features.heatingSystem"]',
            'annee_construction' => '[data-testid="aviv.CDP.Sections.Energy.Features.yearOfConstruction"]'
        ];

        $results = [];

        foreach ($selectors as $key => $selector) {
            $values = $crawler->filter($selector)->each(fn($node) => trim($node->text()));
            $results[$key] = count($values) > 0 ? $values : ['empty'];
        }

        return response()->json($results);
    }
}
