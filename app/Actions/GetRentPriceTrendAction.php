<?php

namespace App\Actions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GetRentPriceTrendAction
{
    private string $apiUrl = "https://data.economie.gouv.fr/api/records/1.0/search/";

    public function execute(string $quartier): ?array
    {
        $client = new Client();

        try {
            // Appel API pour récupérer les prix des loyers dans le quartier
            $response = $client->get($this->apiUrl, [
                'query' => [
                    'dataset' => 'fr-loyers-quartier',  // Exemple de dataset (à adapter selon l'API)
                    'q' => $quartier,
                    'rows' => 12,  // Récupère les prix des 12 derniers mois
                    'sort' => '-date'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['records'])) {
                return null;
            }

            // Extraction des prix des loyers sur plusieurs mois
            $loyers = array_map(fn($record) => $record['fields']['loyer_moyen'] ?? null, $data['records']);
            $loyers = array_filter($loyers); // Supprime les valeurs nulles

            if (count($loyers) < 2) {
                return null;
            }

            // Calcul de la tendance des prix
            $first = reset($loyers);  // Premier mois
            $last = end($loyers);     // Dernier mois

            if ($last > $first) {
                $tendance = "hausse";
            } elseif ($last < $first) {
                $tendance = "baisse";
            } else {
                $tendance = "stable";
            }

            return [
                'quartier' => $quartier,
                'loyer_actuel' => $last,
                'tendance' => $tendance
            ];
        } catch (RequestException $e) {
            error_log("Erreur API Loyers: " . $e->getMessage());
            return null;
        }
    }
}
