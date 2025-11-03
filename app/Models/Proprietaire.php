<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proprietaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'nom',
        'prenom',
        'nom_societe',
        'siret',
        'adresse',
        'code_postal',
        'ville',
        'pays',
        'email',
        'telephone',
        'telephone_secondaire',
        'iban',
        'bic',
        'mandat_actif',
        'date_debut_mandat',
        'date_fin_mandat',
        'notes',
    ];

    protected $casts = [
        'mandat_actif' => 'boolean',
        'date_debut_mandat' => 'date',
        'date_fin_mandat' => 'date',
    ];

    // Relations
    public function biens()
    {
        return $this->hasMany(Bien::class);
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Accesseurs
    public function getNomCompletAttribute()
    {
        if ($this->type === 'societe') {
            return $this->nom_societe;
        }
        return trim($this->prenom . ' ' . $this->nom);
    }
}