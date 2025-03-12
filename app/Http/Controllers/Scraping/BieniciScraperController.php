<?php

namespace App\Http\Controllers\Scraping;

use App\Actions\ScrapingAction;
use App\Http\Requests\ScrapingRequest;

class BieniciScraperController
{
    public function scrape(ScrapingRequest $request)
    {
        $validated = $request->validated();
        $url = $validated['url'];

        return (new ScrapingAction()->execute($url));
    }
}
