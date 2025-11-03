<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Garant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'locataire_id',
        'nom',
        'prenom',
        'date_naissance',
        'adresse',
        'code_postal',
        'ville',
        'pays',
        'email',
        'telephone',
        'profession',
        'revenus_mensuels',
        'lien_avec_locataire',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'revenus_mensuels' => 'decimal:2',
    ];

    // Relations
    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    // Accesseurs
    public function getNomCompletAttribute()
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function getAdresseCompleteAttribute()
    {
        return "{$this->adresse}, {$this->code_postal} {$this->ville}, {$this->pays}";
    }
}