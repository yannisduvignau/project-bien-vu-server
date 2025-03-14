<?php

namespace App\Http\Controllers;

use App\Actions\ScrapingAction;
use App\Http\Requests\ScrapingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Actions\OpenAIRequestAction;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Laravel BienVu API Documentation",
 *      description="Documentation de l'API BienVu avec Swagger"
 * )
 *
 * @OA\PathItem(path="/api")
 */
class ScrapController
{
    public function __construct(
        private OpenAIRequestAction $openIARequest,
    ) {}

    /**
     * Analyse d'une annonce à l'aide d'une url
     *
     * @OA\Post(
     *      path="/api/ia/analyse/url",
     *      tags={"Analyse"},
     *      summary="Analyse d'une annonce à l'aide d'une url",
     *      @OA\Response(
     *          response=200,
     *          description="Succès"
     *      )
     * )
     */
    public function analyserAnnonce(ScrapingRequest $request)
    {
        $validated = $request->validated();
        $url = $validated['url'];

        // Récupérer la description de l'annonce via l'action de scraping
        $description = (new ScrapingAction())->execute($url);

        // Chargement des configurations depuis le .env
        $promptBase = env("PROMPT_ANALYSER");
        $model = env("MODEL_ANALYSER", "gpt-4-turbo");
        $roleSystem = env("ROLE_SYSTEM_ANALYSER", "Vous êtes un expert en analyse d'annonces immobilières.");
        $temperature = (float) env("TEMPERATURE_ANALYSER", 0.5);

        if (!$promptBase) {
            Log::error("PROMPT_ANALYSER est manquant dans le fichier .env");
            return response()->json(['error' => 'Configuration du serveur incorrecte.'], 500);
        }

        // Création du prompt final
        $prompt = sprintf("%s\nAnnonce : %s", $promptBase, $description);

        try {
            // Envoi de la requête à OpenAI
            $output = $this->openIARequest->execute($model, $roleSystem, $prompt, $temperature);
            Log::info("Réponse brute de l'IA : " . $output);

            // Vérification et décodage de la réponse JSON
            $cleanOutput = trim($output, "```json \n"); // Nettoie les éventuelles balises Markdown
            $decodedOutput = json_decode($cleanOutput, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedOutput)) {
                Log::error("Réponse JSON invalide : " . json_last_error_msg());
                return response()->json(['error' => 'Réponse de l\'IA mal formatée.'], 500);
            }

            // Vérification des clés attendues
            $requiredKeys = ['analysis', 'reliability'];
            foreach ($requiredKeys as $key) {
                if (!array_key_exists($key, $decodedOutput)) {
                    Log::error("Réponse JSON incomplète : clé manquante '$key'.");
                    return response()->json(['error' => 'Réponse de l\'IA incomplète.'], 500);
                }
            }

            return response()->json($decodedOutput);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'analyse de l\'annonce : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'analyse de l\'annonce.'], 500);
        }
    }

    /**
     * Estimation d'une annonce à l'aide d'une url
     *
     * @OA\Post(
     *      path="/api/ia/estimation/url",
     *      tags={"Estimation"},
     *      summary="Estimation d'une annonce à l'aide d'une url",
     *      @OA\Response(
     *          response=200,
     *          description="Succès"
     *      )
     * )
     */
    public function estimerPrix(ScrapingRequest $request)
    {
        $validated = $request->validated();
        $url = $validated['url'];

        // Récupérer la description de l'annonce via l'action de scraping
        $description = (new ScrapingAction())->execute($url);

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

            // Vérification et décodage de la réponse JSON
            $cleanOutput = trim($output, "```json \n"); // Nettoie les éventuelles balises Markdown
            $decodedOutput = json_decode($cleanOutput, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedOutput)) {
                Log::error("Réponse JSON invalide : " . json_last_error_msg());
                return response()->json(['error' => 'Réponse de l\'IA mal formatée.'], 500);
            }

            // Vérification des clés attendues
            $requiredKeys = ['prix_min', 'prix_max', 'prix_moyen', 'confiance'];
            foreach ($requiredKeys as $key) {
                if (!array_key_exists($key, $decodedOutput)) {
                    Log::error("Réponse JSON incomplète : clé manquante '$key'.");
                    return response()->json(['error' => 'Réponse de l\'IA incomplète.'], 500);
                }
            }

            return response()->json($decodedOutput);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'estimation de l\'annonce : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'estimation de l\'annonce.'], 500);
        }
    }
}
