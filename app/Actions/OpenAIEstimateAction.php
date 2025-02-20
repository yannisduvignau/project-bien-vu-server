<?php

namespace App\Actions;

use Exception;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIEstimateAction
{

    public function execute(string $texte): ?array
    {
        try {
            $response = OpenAI::completions()->create([
                'model' => 'gpt-4o',
                'prompt' => "Tu es un expert en estimation immobilière.,\n"
                        . "Estime le prix d'un bien immobilier en JSON ('clé':'valeur') avec :\n"
                        . "- Une fourchette de prix : (clés `prix_min` et `prix_max`, type `int`)\n"
                        . "- Un prix moyen par rapport au quartier/localisation : (clé `prix_moyen`, type `int`)\n"
                        . "- La confiance en l'estimation : (clé `confiance`, type `str`)\n"
                        . "Voici la description du bien : $texte.\n"
                        . "Réponds uniquement avec un JSON valide sans aucun texte supplémentaire.",
            ]);

            // 🔹 Extraction du JSON depuis la réponse
            $outputText = trim($response['choices'][0]['message']['content']);

            // 🔹 Vérification et nettoyage du JSON
            $parsedJson = json_decode($outputText, true);
            if ($parsedJson === null) {
                throw new Exception("Réponse OpenAI invalide : " . $outputText);
            }

            // 🔹 Retourne le JSON propre
            return [
                "prix_estime" => $parsedJson
            ];
        } catch (Exception $e) {
            // 🔹 Gestion des erreurs et retour d'un message d'erreur
            return [
                "error" => $e->getMessage()
            ];
        }
    }
}
