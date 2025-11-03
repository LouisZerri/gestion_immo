<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contrat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'bien_id',
        'proprietaire_id',
        'type_bail',
        'date_debut',
        'date_fin',
        'duree_mois',
        'loyer_hc',
        'charges',
        'loyer_cc',
        'depot_garantie',
        'periodicite_paiement',
        'jour_paiement',
        'indice_reference',
        'date_revision',
        'tacite_reconduction',
        'statut',
        'date_signature',
        'conditions_particulieres',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_signature' => 'date',
        'date_revision' => 'date',
        'loyer_hc' => 'decimal:2',
        'charges' => 'decimal:2',
        'loyer_cc' => 'decimal:2',
        'depot_garantie' => 'decimal:2',
        'indice_reference' => 'decimal:2',
        'tacite_reconduction' => 'boolean',
    ];

    // Relations
    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function locataires()
    {
        return $this->belongsToMany(Locataire::class, 'contrat_locataire')
            ->withPivot('titulaire_principal', 'part_loyer')
            ->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function quittancesAutomatisees()
    {
        return $this->hasMany(QuittanceAutomatisee::class);
    }

    // Accesseurs
    public function getLocatairePrincipalAttribute()
    {
        return $this->locataires()->wherePivot('titulaire_principal', true)->first();
    }

    public function getIsCoLocationAttribute()
    {
        return $this->locataires()->count() > 1;
    }

    public function getTypeBailLibelleAttribute()
    {
        $types = [
            'vide' => 'Location vide',
            'meuble' => 'Location meublée',
            'commercial' => 'Bail commercial',
            'professionnel' => 'Bail professionnel',
            'parking' => 'Parking/Garage',
        ];
        
        return $types[$this->type_bail] ?? $this->type_bail;
    }
}