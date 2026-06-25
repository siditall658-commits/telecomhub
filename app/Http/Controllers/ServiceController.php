<?php

namespace App\Http\Controllers;

use App\Models\TelecomService;
use App\Models\Client;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type_service' => 'required|string',
            'numero_ligne' => 'required|string|unique:telecom_services',
        ]);

        TelecomService::create([
            'client_id' => $request->client_id,
            'type_service' => $request->type_service,
            'numero_ligne' => $request->numero_ligne,
            'statut' => 'ACTIF',
        ]);

        return redirect()->route('clients.index')->with('success', 'Service créé avec succès.');
    }

    /**
     * Assigner directement un service à un client depuis son dossier.
     */
    public function storeForClient(Request $request, Client $client)
    {
        $request->validate([
            'type_service' => 'required|in:Fibre,ADSL,Mobile',
        ]);

        // Génération d'un identifiant de ligne unique selon la technologie
        $prefixe = [
            'Fibre'  => 'FTTH-',
            'ADSL'   => 'ADSL-',
            'Mobile' => 'MOB-'
        ][$request->type_service];

        $identifiantLigne = $prefixe . rand(100000, 999999);

        // Création du service rattaché au client (Corrigé avec numero_ligne !)
        $client->services()->create([
            'type_service' => $request->type_service,
            'numero_ligne' => $identifiantLigne,
            'statut' => 'ACTIF',
        ]);

        return redirect()->back()->with('success', 'Le service ' . $request->type_service . ' a été attribué avec succès (Ligne : ' . $identifiantLigne . ').');
    }
}