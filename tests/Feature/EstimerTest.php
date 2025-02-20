<?php

// it('returns a successful (200) response', function () {
//     $data = ["description" => 'Une description d\'annonce.'];
//     $response = $this->postJson('/api/ia/estimation', $data, ['Accept' => 'application/json']);

//     $response->assertStatus(200);
// })->group('api','estimer');

it('returns an error (404) response', function () {
    $data = ["description" => 'Une description d\'annonce.'];
    $response = $this->postJson('/api/ia/estimer', $data, ['Accept' => 'application/json']);

    $response->assertStatus(404);
})->group('api','estimer');
