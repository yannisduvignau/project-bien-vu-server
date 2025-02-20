<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\ScrapingRequest;
use Illuminate\Support\Facades\Validator;

class ScrapingRequestTest extends TestCase
{
    public function test_scraping_request_with_valid_data_passes_validation()
    {
        $data = ['url' => 'https://example.com'];

        $request = new ScrapingRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_scraping_request_without_url_fails_validation()
    {
        $data = [];

        $request = new ScrapingRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('url', $validator->errors()->toArray());
    }

    public function test_scraping_request_with_non_string_url_fails_validation1()
    {
        $data = ['url' => 12345];

        $request = new ScrapingRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('url', $validator->errors()->toArray());
    }

    public function test_scraping_request_with_non_string_url_fails_validation2()
    {
        $data = ['url' => 'Ceci est une mauvaise url'];

        $request = new ScrapingRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('url', $validator->errors()->toArray());
    }
}
