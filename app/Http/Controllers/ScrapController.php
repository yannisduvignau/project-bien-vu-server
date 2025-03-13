<?php

namespace App\Http\Controllers;

use App\Actions\ScrapingAction;
use App\Http\Requests\ScrapingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Actions\OpenAIRequestAction;
use Illuminate\Support\Facades\Log;

class ScrapController
{
    public function __construct(
        private OpenAIRequestAction $openIARequest,
    ) {}

    public function analyserAnnonce(ScrapingRequest $request)
    {
        $validated = $request->validated();
        $url = $validated['url'];

        $description = (new ScrapingAction()->execute($url));

        $promptBase = env("PROMPT_ANALYSER");
        $model = env("MODEL_ANALYSER", "gpt-4-turbo");
        $roleSystem = env("ROLE_SYSTEM_ANALYSER", "Vous êtes un expert en analyse d'annonces immobilières.");
        $temperature = env("TEMPERATURE_ANALYSER", 0.5);

        if (!$promptBase) {
            Log::error("PROMPT_ANALYSER est manquant dans le fichier .env");
            return response()->json(['error' => 'Configuration du serveur incorrecte.'], 500);
        }

        $prompt = sprintf("%s\nAnnonce : %s", $promptBase, $description);

        try {
            $output = $this->openIARequest->execute($model, $roleSystem, $prompt, (float) $temperature);
            Log::info("Réponse brute de l'IA : " . $output);

            $decodedOutput = json_decode($output, true);
            // if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedOutput)) {
            //     Log::error("Réponse JSON invalide : " . $output);
            //     return response()->json(['error' => 'Réponse de l\'IA mal formatée.'], 500);
            // }

            return response()->json($decodedOutput);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'analyse de l\'annonce : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'analyse de l\'annonce.'], 500);
        }
    }

    public function estimerPrix(ScrapingRequest $request)
    {
        $validated = $request->validated();
        $url = $validated['url'];

        $description = (new ScrapingAction()->execute($url));

        $promptBase = env("PROMPT_ESTIMER");
        $model = env("MODEL_ESTIMER", "gpt-4-turbo");
        $roleSystem = env("ROLE_SYSTEM_ESTIMER", "Vous êtes un expert en estimation immobilière.");
        $temperature = env("TEMPERATURE_ESTIMER", 0.5);

        if (!$promptBase) {
            Log::error("PROMPT_ESTIMER est manquant dans le fichier .env");
            return response()->json(['error' => 'Configuration du serveur incorrecte.'], 500);
        }

        $prompt = sprintf("%s\nAnnonce : %s", $promptBase, $description);

        try {
            $output = $this->openIARequest->execute($model, $roleSystem, $prompt, (float) $temperature);
            Log::info("Réponse brute de l'IA : " . $output);

            $decodedOutput = json_decode($output, true);
            // if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedOutput)) {
            //     Log::error("Réponse JSON invalide : " . $output);
            //     return response()->json(['error' => 'Réponse de l\'IA mal formatée.'], 500);
            // }

            return response()->json($decodedOutput);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'estimation de l\'annonce : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'estimation de l\'annonce.'], 500);
        }
    }
}
