<?php

namespace App\Http\Controllers;

use App\Actions\OpenAIEstimateAction;
use App\Actions\ScriptMistralAction;
use App\Http\Requests\AnalyserRequest;
use App\Http\Requests\EstimerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IAController extends Controller
{
    public function __construct(
        private ScriptMistralAction $executeScript
    ) {
    }

    /**
     * POST : Analyses a property advertisement and detects inconsistencies or errors.
     */
    public function analyserAnnonce(AnalyserRequest $request)
    {
        $validated = $request->validated();
        $description = $validated['description'];

        $script = storage_path('scripts/analyser_annonce.py');
        $output = $this->executeScript->execute($script, $description);
        Log::info("Réponse brute de l'IA : " . $output);

        $response = $this->cleaningIAResponseMd($output);

        return response()->json($response);

        // return response()->json([
        //     'coherence' => $response['coherence'],
        //     'erreurs' => $response['erreurs'],
        //     'fiabilite' => $response['fiabilite']
        // ]);
    }


    /**
     * POST : Estimate the price of a property by extracting information from a text.
     */
    public function estimerPrix(EstimerRequest $request)
    {
        $validated = $request->validated();
        $description = $validated['description'];

        $script = storage_path('scripts/estimer_prix.py');
        $output = $this->executeScript->execute($script, $description);
        Log::info("Réponse brute de l'IA : " . $output);

        $response = $this->cleaningIAResponseMd($output);

        // return response()->json([
        //     'prix_min' => $response['prix_min'] ?? null,
        //     'prix_max' => $response['prix_max'] ?? null,
        //     'prix_moyen' => isset($response['prix_moyen']) ? round($response['prix_moyen']) : null,
        //     'confiance' => $response['confiance'] ?? null
        // ]);

        return $response;
    }
    public function estimerPrixOpenAI(EstimerRequest $request)
    {
        $validated = $request->validated();
        $description = $validated['description'];

        // Appel de l'action OpenAI pour estimer le prix
        $openAIEstimateAction = new OpenAIEstimateAction();
        $result = $openAIEstimateAction->execute($description);

        if (isset($result['error'])) {
            return response()->json([
                'message' => $result['error']
            ], 500);
        }

        return response()->json([
            'prix_estime' => $result['prix_estime']
        ]);
    }

    /**
     * POST : Generate an optimised property ad text.
     */
    public function genererAnnonce(Request $request)
    {
        $type = $request->input('type');
        $surface = $request->input('surface');
        $pieces = $request->input('pieces');
        $ville = $request->input('ville');

        $data = [
            'type' => $type,
            'surface' => $surface,
            'pieces' => $pieces,
            'ville' => $ville
        ];

        $script = storage_path('scripts/generer_annonce.py');

        $output = $this->executeScript->execute($script, $data);
        Log::info("Réponse brute de l'IA : " . $output);

        // 🔹 Étape 1 : Décoder la première réponse JSON
        $decodedOutput = json_decode($output, true);

        if (!isset($decodedOutput['data'])) {
            Log::error("Réponse mal formatée : " . $output);
            return response()->json([
                'message' => 'Erreur lors de l\'extraction des données'
            ], 500);
        }

        $annonce = $decodedOutput['data']; // Contient le JSON sous forme de texte + explications

        return response()->json(['annonce' => $annonce]);
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
