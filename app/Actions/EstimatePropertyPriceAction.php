<?php

namespace App\Actions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class EstimatePropertyPriceAction
{
    private string $apiUrl = "https://data.economie.gouv.fr/api/records/1.0/search/";

    public function execute(string $description): ?array
    {
        // Extraction des caractéristiques du bien
        $caracteristiques = $this->analyserDescription($description);

        if (!$caracteristiques) {
            return null;
        }

        $client = new Client();

        try {
            // Requête à une API de transactions immobilières pour récupérer des biens similaires
            $response = $client->get($this->apiUrl, [
                'query' => [
                    'dataset' => 'fr-prix-immobilier', // Exemple d'API, à adapter selon la source de données
                    'q' => "{$caracteristiques['ville']} AND surface >= {$caracteristiques['surface']} - 5 AND surface <= {$caracteristiques['surface']} + 5",
                    'rows' => 10, // On récupère 10 transactions similaires
                    'sort' => '-date_mutation'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['records'])) {
                return null;
            }

            // Récupération des prix des biens similaires
            $prixBiens = array_map(fn($record) => $record['fields']['valeur_fonciere'] ?? null, $data['records']);
            $prixBiens = array_filter($prixBiens); // Supprime les valeurs nulles

            if (count($prixBiens) < 2) {
                return null;
            }

            // Calcul du prix moyen et de l'écart type
            $prixMoyen = round(array_sum($prixBiens) / count($prixBiens), 2);
            $prixMin = min($prixBiens);
            $prixMax = max($prixBiens);

            return [
                'description' => $description,
                'ville' => $caracteristiques['ville'],
                'surface' => $caracteristiques['surface'],
                'pieces' => $caracteristiques['pieces'],
                'prix_estime' => $prixMoyen,
                'prix_min' => $prixMin,
                'prix_max' => $prixMax
            ];
        } catch (RequestException $e) {
            error_log("Erreur API Immobilier: " . $e->getMessage());
            return null;
        }
    }

    private function analyserDescription(string $description): ?array
    {
        // Extraction simplifiée des caractéristiques depuis la description
        preg_match('/(\d+)\s*m²/', $description, $surfaceMatch);
        preg_match('/(\d+)\s*pièce[s]?/', $description, $piecesMatch);
        preg_match('/à\s+([A-Za-z\s\-]+)/', $description, $villeMatch);

        $surface = $surfaceMatch[1] ?? null;
        $pieces = $piecesMatch[1] ?? null;
        $ville = $villeMatch[1] ?? null;

        if (!$surface || !$pieces || !$ville) {
            return null;
        }

        return [
            'surface' => (int) $surface,
            'pieces' => (int) $pieces,
            'ville' => trim($ville)
        ];
    }
}
