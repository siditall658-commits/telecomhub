<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelecomService extends Model
{
    protected $fillable = ['client_id', 'type_service', 'numero_ligne', 'statut'];

    // Garde l'ancienne relation avec le client
    public function client() 
    {
        return $this->belongsTo(Client::class);
    }

    // AJOUTE UNIQUEMENT CECI POUR LES INCIDENTS :
    public function incidents() 
    {
        return $this->hasMany(Incident::class);
    }
}