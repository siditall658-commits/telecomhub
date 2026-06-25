<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être remplis en masse.
     * C'est ici qu'on ajoute 'technicien'.
     */
   protected $fillable = ['telecom_service_id', 'titre', 'description', 'priorite', 'technicien', 'statut'];

    /**
     * Relation : Un incident appartient à un service télécom.
     */
    public function service()
    {
        return $this->belongsTo(TelecomService::class, 'telecom_service_id');
    }
}