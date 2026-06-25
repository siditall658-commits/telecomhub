<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['nom', 'email', 'telephone', 'adresse'];

    // Relation : Un client a plusieurs services
    public function services() 
    {
        return $this->hasMany(TelecomService::class);
    }
}