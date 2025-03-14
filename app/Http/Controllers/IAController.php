<?php

namespace App\Http\Controllers;

use App\Actions\OpenAIRequestAction;
use App\Actions\ScriptMistralAction;
use App\Http\Requests\AnalyserRequest;
use App\Http\Requests\EstimerRequest;
use App\Http\Requests\GenererRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class IAController extends Controller
{
    public function __construct(
        private ScriptMistralAction $executeScript,
        private OpenAIRequestAction $openIARequest,
    ) {}

    /**
     * POST : Analyses a property advertisement and detects inconsistencies or errors.
     */
    // public function analyserAnnonce(AnalyserRequest $request)
    // {
    //     $validated = $request->validated();
    //     $description = $validated['description'];

    //     $cleanedDescription = preg_replace("/['\",]/", "", $description);

    //     $script = storage_path('scripts/analyser_annonce.py');
    //     $output = $this->executeScript->execute($script, $cleanedDescription);
    //     Log::info("Réponse brute de l'IA : " . $output);

    //     $response = $this->cleaningIAResponseMd($output);

    //     return response()->json($response);
    // }

    public function analyserAnnonce(AnalyserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $description = trim(strip_tags($validated['description']));

        $promptBase = env("PROMPT_ANALYSER");
        $model = env("MODEL_ANALYSER", "gpt-4-turbo");
        $roleSystem = env("ROLE_SYSTEM_ANALYSER", "Vous êtes un expert en analyse d'annonces immobilières.");
        $temperature = env("TEMPERATURE_ANALYSER", 0.5);

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
     * POST : Estimate the price of a property by extracting information from a text.
     */
    public function estimerPrix(EstimerRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $description = trim(strip_tags($validated['description']));

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

    /**
     * POST : Generate an optimised property ad text.
     */
    public function genererAnnonce(GenererRequest $request): JsonResponse
    {
        // Validation stricte des données entrantes
        $validated = $request->validated();

        // Vérification des variables d'environnement
        $promptTemplate = env("PROMPT_GENERER");
        $model = env("MODEL_GENERER", "gpt-4-turbo");
        $roleSystem = env("ROLE_SYSTEM_GENERER", "Vous êtes un expert en rédaction d'annonces immobilières.");
        $temperature = env("TEMPERATURE_GENERER", 0.5);

        if (!$promptTemplate) {
            Log::error("PROMPT_GENERER est manquant dans le fichier .env");
            return response()->json(['error' => 'Configuration du serveur incorrecte.'], 500);
        }

        // Construction sécurisée du prompt
        $prompt = sprintf(
            "%s\nType: %s\nSurface: %s m²\nPièces: %d\nVille: %s",
            $promptTemplate,
            e($validated['type']),
            e($validated['surface']),
            (int) $validated['pieces'],
            e($validated['ville'])
        );

        try {
            $output = $this->openIARequest->execute($model, $roleSystem, $prompt, (float) $temperature);
            Log::info("Réponse brute de l'IA : " . $output);

            return response()->json(['annonce' => $output]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de l\'annonce : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la génération de l\'annonce.'], 500);
        }
    }



    /**
     * Extraction des informations depuis un texte brut.
     */
    private function extraireInformations($texte)
    {
        // Extraction par expressions régulières
        preg_match('/(appartement|maison|studio|villa|loft)/i', $texte, $type);
        preg_match('/(\d+)\s?(m²|m2)/i', $texte, $surface);
        preg_match('/(\d+)\s?(pièce|chambre|pièces|chambres)/i', $texte, $pieces);
        preg_match('/(\d{5})/', $texte, $code_postal);
        preg_match('/(neuf|bon état|rénové|ancien)/i', $texte, $etat);
        preg_match('/(\d{1,3}(?:[\s,]\d{3})*(?:[\.,]?\d{2})?)\s?€?/i', $texte, $prix); // Expression améliorée pour capturer les prix avec des espaces ou des virgules
        preg_match('/(Paris|Lyon|Marseille|Bordeaux|Nice|Toulouse|Nantes)/i', $texte, $ville);

        // Nettoyage des résultats
        return [
            'type' => $type[1] ?? 'appartement',
            'surface' => isset($surface[1]) ? intval(str_replace([' ', ','], '', $surface[1])) : null, // Nettoyage du texte
            'pieces' => isset($pieces[1]) ? intval($pieces[1]) : null,
            'ville' => $ville[1] ?? 'Non spécifié',
            'code_postal' => $code_postal[1] ?? 'Non spécifié',
            'etat' => $etat[1] ?? 'bon état',
            'prix' => isset($prix[1]) ? intval(str_replace([' ', ','], '', $prix[1])) : null // Nettoyage du prix
        ];
    }

    private function cleaningIAResponseMd($output)
    {
        // 🔹 Étape 1 : Décoder la première réponse JSON
        $decodedOutput = json_decode($output, true);

        if (!isset($decodedOutput['data'])) {
            Log::error("Réponse mal formatée : " . $output);
            return response()->json([
                'message' => 'Erreur lors de l\'extraction des données'
            ], 500);
        }

        $prixEstime = $decodedOutput['data']; // Contient le JSON sous forme de texte + explications

        // 🔹 Étape 2 : Extraire le JSON de la réponse de l'IA
        $jsonString = null;

        if (preg_match('/```json\s*([\s\S]*?)\s*```/', $prixEstime, $matches)) {
            $jsonString = $matches[1]; // Extraction propre du JSON
        } elseif (preg_match('/\{.*?\}/s', $prixEstime, $matches)) {
            $jsonString = $matches[0]; // Extraction alternative
        }

        if (!$jsonString) {
            Log::error("Erreur lors de l'extraction du JSON : " . $prixEstime);
            return response()->json([
                'message' => 'Erreur lors de l\'extraction des données'
            ], 500);
        }

        // 🔹 Étape 3 : Nettoyage et décodage du JSON extrait
        $jsonString = trim($jsonString);
        $jsonString = preg_replace('/[\x00-\x1F\x7F]/u', '', $jsonString);

        Log::info("Après extraction et nettoyage : " . $jsonString);

        $response = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("Erreur de décodage JSON : " . json_last_error_msg());
            return response()->json([
                'message' => 'Erreur lors de l\'estimation du prix'
            ], 500);
        }

        return $response;
    }
}
