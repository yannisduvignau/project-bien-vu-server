<?php

namespace App\Http\Controllers;

use App\Actions\EstimatePropertyPriceAction;
use App\Actions\GetDpeValueAction;
use App\Actions\GetRentPriceTrendAction;
use Illuminate\Http\Request;

class Controller
{
    public function getDpe(Request $request, GetDpeValueAction $getDpeValueAction)
    {
        // Récupérer l'adresse depuis la requête
        $adresse = $request->input('adresse');

        // Vérifier si l'adresse est fournie
        if (!$adresse) {
            return response()->json(['error' => 'Adresse requise'], 400);
        }

        // Appeler l'action pour récupérer les données du DPE
        $dpeData = $getDpeValueAction->execute($adresse);

        // Vérifier si on a trouvé un résultat
        if (!$dpeData) {
            return response()->json(['error' => 'DPE non trouvé pour cette adresse'], 404);
        }

        // Retourner les données du DPE
        return response()->json(['conso' => $dpeData], 200);
    }

    public function getRentTrend(Request $request, GetRentPriceTrendAction $getRentPriceTrendAction)
    {
        $quartier = $request->input('quartier');

        if (!$quartier) {
            return response()->json(['error' => 'Quartier requis'], 400);
        }

        $data = $getRentPriceTrendAction->execute($quartier);

        if (!$data) {
            return response()->json(['error' => 'Données indisponibles pour ce quartier'], 404);
        }

        return response()->json(['data' => $data], 200);
    }

    public function estimatePropertyPrice(Request $request, EstimatePropertyPriceAction $estimatePropertyPriceAction)
    {
        $description = $request->input('description');

        if (!$description) {
            return response()->json(['error' => 'Description requise'], 400);
        }

        $data = $estimatePropertyPriceAction->execute($description);

        if (!$data) {
            return response()->json(['error' => 'Impossible d\'estimer le prix'], 404);
        }

        return response()->json(['data' => $data], 200);
    }
}
