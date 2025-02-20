<?php

namespace App\Actions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GetDpeValueAction
{
    private string $apiUrl = "https://data.ademe.fr/data-fair/api/v1/datasets/dpe-france/lines";

    public function execute(string $adresse): ?array
    {
        $client = new Client();

        try {
            $response = $client->get($this->apiUrl, [
                'query' => [
                    'q' => $adresse,
                    'size' => 1  // Limite le nombre de résultats
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return $data['results'][0] ?? null;
        } catch (RequestException $e) {
            // Gestion des erreurs de requête
            error_log("Erreur API DPE: " . $e->getMessage());
            return null;
        }
    }
}
