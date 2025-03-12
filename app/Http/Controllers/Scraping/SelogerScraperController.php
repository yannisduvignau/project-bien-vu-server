<?php

namespace App\Http\Controllers\Scraping;

use App\Actions\ScrapingAction;
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

        return (new ScrapingAction()->execute($url));
    }
}
