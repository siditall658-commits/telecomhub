<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\TelecomService;
use App\Models\Incident;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // --- LOGIQUE DES STATISTIQUES GLOBALES ---
        $totalClients = Client::count();
        $totalServicesActifs = TelecomService::where('statut', 'ACTIF')->count();
        $totalTicketsOuverts = Incident::where('statut', 'Ouvert')->count();

        // --- STATISTIQUES DE SANTE DU RESEAU ---
        $pannesFibre = TelecomService::where('type_service', 'Fibre')->where('statut', 'EN PANNE')->count();
        $pannesADSL  = TelecomService::where('type_service', 'ADSL')->where('statut', 'EN PANNE')->count();
        $pannesMobile = TelecomService::where('type_service', 'Mobile')->where('statut', 'EN PANNE')->count();

        // --- RECUPERATION DES LOGS D'ACTIVITE ---
        $logs = ActivityLog::latest()->take(10)->get();

        // --- LOGIQUE DE RECHERCHE ET FILTRES ---
        $query = Client::query();

        $query->with(['services.incidents' => function($q) {
            $q->where('statut', 'Ouvert');
        }]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('en_panne')) {
            $query->whereHas('services', function($q) {
                $q->where('statut', 'EN PANNE');
            });
        }

        $clients = $query->withCount('services')->get();

        // --- TRI INTELLIGENT PAR GRAVITÉ ---
        $clients = $clients->sortByDesc(function($client) {
            $hasCritical = false;
            foreach($client->services as $service) {
                foreach($service->incidents as $incident) {
                    if (in_array(strtolower($incident->priorite), ['haute', 'critique', 'high'])) {
                        $hasCritical = true;
                    }
                }
            }
            return $hasCritical ? 2 : ($client->services()->where('statut', 'EN PANNE')->exists() ? 1 : 0);
        });

        return view('clients.index', compact(
            'clients', 
            'totalClients', 
            'totalServicesActifs', 
            'totalTicketsOuverts',
            'pannesFibre',
            'pannesADSL',
            'pannesMobile',
            'logs'
        ));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'telephone' => 'required|string|max:20',
        ]);

        Client::create($request->all());
        return redirect()->route('clients.index')->with('success', 'Client créé avec succès.');
    }

    public function show(Client $client)
    {
        $client->load('services.incidents');
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients,email,' . $client->id,
            'telephone' => 'required|string|max:20',
        ]);

        $client->update($request->all());
        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Client $client)
{
    foreach ($client->services as $service) {
        $service->incidents()->delete();
        $service->delete();
    }
$client->delete();

    return redirect()->route('clients.index')->with('success', 'Le dossier du client et l\'historique de ses lignes ont été supprimés avec succès.');
}
}