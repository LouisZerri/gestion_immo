<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'adresse',
        'code_postal',
        'ville',
        'pays',
        'type',
        'surface',
        'nombre_pieces',
        'etage',
        'dpe',
        'statut',
        'description',
        'photos',
        'rentabilite',
        'proprietaire_id',
    ];

    protected $casts = [
        'surface' => 'decimal:2',
        'rentabilite' => 'decimal:2',
    ];

    // Relations
    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
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
    public function getAdresseCompleteAttribute()
    {
        return "{$this->adresse}, {$this->code_postal} {$this->ville}, {$this->pays}";
    }

    public function getTypeLibelleAttribute()
    {
        $types = [
            'appartement' => 'Appartement',
            'maison' => 'Maison',
            'studio' => 'Studio',
            'parking' => 'Parking',
            'garage' => 'Garage',
            'local_commercial' => 'Local commercial',
            'bureau' => 'Bureau',
            'terrain' => 'Terrain',
        ];
        
        return $types[$this->type] ?? $this->type;
    }
}