<?php

namespace App\Http\Controllers;

use App\Models\Incidents;
use App\Models\TelecomService;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    /**
     * Enregistrer un nouvel incident (Bouton rouge).
     */
    public function store(Request $request)
    {
        $request->validate([
            'telecom_service_id' => 'required|exists:telecom_services,id',
            'titre' => 'required|string|max:255',
            'priorite' => 'required|string',
            'technicien' => 'nullable|string|max:255',
        ]);

        Incidents::create([
            'telecom_service_id' => $request->telecom_service_id,
            'titre'              => $request->titre,
            'description'        => $request->titre, 
            'priorite'           => $request->priorite,
            'technicien'         => $request->technicien ?? 'Non assigné',
            'statut'             => 'Ouvert',
        ]);

        // Passer le service associé en panne
        $service = TelecomService::find($request->telecom_service_id);
        if ($service) {
            $service->update(['statut' => 'EN PANNE']);
        }

        return redirect()->back()->with('success', 'Incident signalé et affecté avec succès.');
    }

    /**
     * Résoudre un incident existant (Bouton vert).
     */
    public function resolve(Incident $incident)
    {
        // 1. On passe le ticket en Résolu
        $incident->update(['statut' => 'Résolu']);

        // 2. On passe la ligne téléphonique/internet associée à "ACTIF"
        if ($incident->service) {
            $incident->service->update(['statut' => 'ACTIF']);
        }

        return redirect()->back()->with('success', 'Ligne réparée et incident marqué comme Résolu.');
    }
}