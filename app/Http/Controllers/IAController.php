<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IAController extends Controller
{
    /**
     * Analyse une annonce immobilière et détecte les incohérences ou erreurs.
     */
    public function analyserAnnonce(Request $request)
    {
        $texte = $request->input('texte');

        if (!$texte) {
            return response()->json([
                'message' => 'Aucune donnée fournie'
            ], 400);
        }

        // Extraction des informations
        $data = $this->extraireInformations($texte);

        if (!$data) {
            return response()->json([
                'message' => 'Impossible d’extraire les informations'
            ], 400);
        }

        // Vérification des incohérences
        $erreurs = [];

        if ($data['prix'] < 50000 || $data['prix'] > 2000000) {
            $erreurs[] = "Le prix semble incohérent.";
        }

        if ($data['surface'] < 15 && $data['prix'] > 500000) {
            $erreurs[] = "Surface trop petite pour un tel prix.";
        }

        // Score de qualité simulé
        $qualite_score = rand(50, 100);

        return response()->json([
            'coherence' => empty($erreurs),
            'erreurs' => $erreurs,
            'qualite_score' => $qualite_score
        ]);
    }

    /**
     * Estime le prix d'un bien immobilier en extrayant les informations d’un texte.
     */
    public function estimerPrix(Request $request)
    {
        $texte = $request->input('texte');

        if (!$texte) {
            return response()->json([
                'message' => 'Aucune donnée fournie'
            ], 400);
        }

        // Extraction des informations
        $data = $this->extraireInformations($texte);

        if (!$data) {
            return response()->json([
                'message' => 'Impossible d’extraire les informations'
            ], 400);
        }

        // Simulation d'une estimation (exemple basique)
        $base_prix_m2 = 5000; // Prix moyen au m² fictif
        $coeff_etat = ($data['etat'] == 'neuf') ? 1.2 : (($data['etat'] == 'bon') ? 1 : 0.8);

        $prix_moyen = $data['surface'] * $base_prix_m2 * $coeff_etat;
        $prix_min = round($prix_moyen * 0.9);
        $prix_max = round($prix_moyen * 1.1);
        $confiance = rand(80, 95);

        return response()->json([
            'prix_min' => $prix_min,
            'prix_max' => $prix_max,
            'prix_moyen' => round($prix_moyen),
            'confiance' => $confiance
        ]);
    }

    /**
     * Génère une annonce immobilière à partir des informations extraites.
     */
    // public function genererAnnonce(Request $request)
    // {
    //     $texte = $request->input('texte');

    //     if (!$texte) {
    //         return response()->json([
    //             'message' => 'Aucune donnée fournie'
    //         ], 400);
    //     }

    //     // Extraction des informations
    //     $data = $this->extraireInformations($texte);

    //     if (!$data) {
    //         return response()->json([
    //             'message' => 'Impossible d’extraire les informations'
    //         ], 400);
    //     }

    //     // Génération de l’annonce
    //     $annonce = "Découvrez ce magnifique {$data['type']} de {$data['surface']} m² situé à {$data['ville']}. " .
    //                "Composé de {$data['pieces']} pièces, ce bien {$data['etat']} est une opportunité rare. " .
    //                "Prix : {$data['prix']} €.";

    //     return response()->json([
    //         'annonce' => $annonce
    //     ]);
    // }
    /**
     * Génère un texte d’annonce immobilière optimisé.
     */
    public function genererAnnonce(Request $request)
    {
        $data = $request->validate([
            'type'                   => 'required|string|in:appartement,maison',
            'surface'                => 'required|numeric|min:10',
            'pieces'                 => 'required|integer|min:1',
            'ville'                  => 'required|string',
            'quartier'               => 'nullable|string',
            'prix'                   => 'required|numeric|min:10000',
            'description_supplementaire' => 'nullable|string',
        ]);

        // Construire l'annonce
        $annonce = "Découvrez cette superbe " . $data['type'] . " de " . $data['surface'] . " m² située à " . $data['ville'];

        if (!empty($data['quartier'])) {
            $annonce .= ", dans le quartier prisé de " . $data['quartier'];
        }

        $annonce .= ". Avec ses " . $data['pieces'] . " pièces spacieuses";

        if (!empty($data['description_supplementaire'])) {
            $annonce .= ", " . $data['description_supplementaire'];
        }

        $annonce .= ", ce bien est une opportunité rare sur le marché. Prix : " . number_format($data['prix'], 0, ',', ' ') . "€.";

        return response()->json([
            'annonce' => $annonce,
        ], 200);
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
        preg_match('/(\d{5,8})\s?€?/i', $texte, $prix);
        preg_match('/(Paris|Lyon|Marseille|Bordeaux|Nice|Toulouse|Nantes)/i', $texte, $ville);

        // Nettoyage des résultats
        return [
            'type' => $type[1] ?? 'appartement',
            'surface' => isset($surface[1]) ? intval($surface[1]) : null,
            'pieces' => isset($pieces[1]) ? intval($pieces[1]) : null,
            'ville' => $ville[1] ?? 'Non spécifié',
            'code_postal' => $code_postal[1] ?? 'Non spécifié',
            'etat' => $etat[1] ?? 'bon état',
            'prix' => isset($prix[1]) ? intval($prix[1]) : null
        ];
    }
}
