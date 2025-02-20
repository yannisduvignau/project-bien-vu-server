<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\AnalyserRequest;
use Illuminate\Support\Facades\Validator;

class AnalyserRequestTest extends TestCase
{
    public function test_analyser_request_with_valid_data_passes_validation()
    {
        $data = ['description' => 'Ceci est une description valide.'];

        $request = new AnalyserRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_analyser_request_without_description_fails_validation()
    {
        $data = [];

        $request = new AnalyserRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('description', $validator->errors()->toArray());
    }

    public function test_analyser_request_with_non_string_description_fails_validation()
    {
        $data = ['description' => 12345];

        $request = new AnalyserRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('description', $validator->errors()->toArray());
    }
}
