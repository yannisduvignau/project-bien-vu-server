<?php

it('returns a successful (200) response', function () {
    $data = ["description" => 'Une description d\'annonce.'];
    $response = $this->postJson('/api/ia/analyse', $data, ['Accept' => 'application/json']);

    $response->assertStatus(200);
})->group('api','analyser');

it('returns an error (404) response', function () {
    $data = ["description" => 'Une description d\'annonce.'];
    $response = $this->postJson('/api/ia/analyser', $data, ['Accept' => 'application/json']);

    $response->assertStatus(404);
})->group('api','analyser');
