<?php

namespace App\Observers;

use App\Models\Incident;
use App\Models\ActivityLog;

class IncidentObserver
{
    /**
     * Déclenché quand un nouvel incident est créé.
     */
    public function created(Incident $incident): void
    {
        $serviceType = $incident->service->type_service ?? 'Service inconnu';
        $clientNom = $incident->service->client->nom ?? 'Client inconnu';
        $tech = $incident->technicien ?? 'un technicien';

        ActivityLog::create([
            'description' => "⚠️ Incident {$incident->priorite} ouvert pour {$clientNom} ({$serviceType}) — Assigné à : {$tech} [🔥 Action requise]",
            'type' => 'INCIDENT',
        ]);
    }

    /**
     * Déclenché quand un incident est mis à jour (ex: résolu).
     */
    public function updated(Incident $incident): void
    {
        if ($incident->isDirty('statut') && $incident->statut === 'Résolu') {
            $serviceType = $incident->service->type_service ?? 'Service inconnu';
            $clientNom = $incident->service->client->nom ?? 'Client inconnu';
            $tech = $incident->technicien ?? 'le technicien';

            ActivityLog::create([
                'description' => "✅ Incident résolu avec succès par {$tech} pour {$clientNom} ({$serviceType})",
                'type' => 'RESOLUTION',
            ]);
        }
    }
}