<?php
// use App\Http\Controllers\Scraping\SelogerScraperController;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;

// test('scrape method returns error if URL is missing', function () {
//     $controller = new SelogerScraperController();
//     $request = Request::create('/scrape', 'GET', []); // Simule une requête sans URL

//     $response = $controller->scrape($request);

//     expect($response->getStatusCode())->toBe(400)
//         ->and($response->getData(true))->toHaveKey('error', 'URL is required');
// });

// test('scrape method returns error if HTTP request fails', function () {
//     Http::fake([
//         'https://example.com' => Http::response(null, 500)
//     ]);

//     $controller = new SelogerScraperController();
//     $request = Request::create('/scrape', 'GET', ['url' => 'https://example.com']);

//     $response = $controller->scrape($request);

//     expect($response->getStatusCode())->toBe(500)
//         ->and($response->getData(true))->toHaveKey('error', 'Failed to fetch the URL');
// });

// test('scrape method returns scraped data correctly', function () {
//     $fakeHtml = <<<HTML
//     <html>
//         <body>
//             <div class="Summarystyled__Title-sc">Appartement de luxe</div>
//             <div class="Summarystyled__PriceText-sc">500 000€</div>
//             <div class="Localizationstyled__Title-sc">Nice, France</div>
//             <div class="TitledDescription__TitledDescriptionContent-sc">Vue sur la mer</div>
//         </body>
//     </html>
//     HTML;

//     Http::fake([
//         'https://example.com' => Http::response($fakeHtml, 200)
//     ]);

//     $controller = new SelogerScraperController();
//     $request = Request::create('/scrape', 'GET', ['url' => 'https://example.com']);

//     $response = $controller->scrape($request);
//     $data = $response->getData(true);

//     expect($response->getStatusCode())->toBe(200)
//         ->and($data['titre_annonce'])->toContain('Appartement de luxe')
//         ->and($data['localisation'])->toContain('Nice, France')
//         ->and($data['description'])->toContain('Vue sur la mer');
// });
