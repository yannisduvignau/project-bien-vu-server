<?php

// it('returns a successful (200) response', function () {
//     $data = ["description" => 'Maison à louer, 5 pièces, 2 chambres.à Bordeaux au Chartrons. À découvrir sans tarder, cette maison de 100 m² environ. 800 euros.'];
//     $response = $this->postJson('/api/ia/estimation', $data, ['Accept' => 'application/json']);

//     $response->assertStatus(200);
// })->group('api','estimer');

it('returns an error (404) response', function () {
    $data = ["description" => 'Une description d\'annonce.'];
    $response = $this->postJson('/api/ia/estimer', $data, ['Accept' => 'application/json']);

    $response->assertStatus(404);
})->group('api','estimer');
