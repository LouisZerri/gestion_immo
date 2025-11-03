<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locataire extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'adresse_actuelle',
        'code_postal',
        'ville',
        'pays',
        'email',
        'telephone',
        'telephone_secondaire',
        'profession',
        'employeur',
        'revenus_mensuels',
        'notes',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'revenus_mensuels' => 'decimal:2',
    ];

    // Relations
    public function contrats()
    {
        return $this->belongsToMany(Contrat::class, 'contrat_locataire')
            ->withPivot('titulaire_principal', 'part_loyer')
            ->withTimestamps();
    }

    public function garants()
    {
        return $this->hasMany(Garant::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Accesseurs
    public function getNomCompletAttribute()
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function getAdresseCompleteAttribute()
    {
        return "{$this->adresse_actuelle}, {$this->code_postal} {$this->ville}, {$this->pays}";
    }

    public function getAgeAttribute()
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }
}