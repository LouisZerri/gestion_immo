<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'type',
        'contenu',
        'actif',
        'is_default',
        'biens_concernes',
        'logo_path',
        'signature_path',
        'footer_text',
        'settings',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'is_default' => 'boolean',
        'biens_concernes' => 'array',
        'settings' => 'array',
    ];

    // Relations
    public function documents()
    {
        return $this->hasMany(Document::class, 'template_id');
    }

    public function quittancesAutomatisees()
    {
        return $this->hasMany(QuittanceAutomatisee::class, 'template_id');
    }

    // Accesseurs
    public function getTypeLibelleAttribute()
    {
        $types = [
            'bail_vide' => 'Contrat de bail - vide',
            'bail_meuble' => 'Contrat de bail - meublé',
            'bail_commercial' => 'Bail commercial',
            'bail_parking' => 'Bail parking/garage',
            'etat_lieux_entree' => 'État des lieux d\'entrée',
            'etat_lieux_sortie' => 'État des lieux de sortie',
            'quittance_loyer' => 'Quittance de loyer',
            'avis_echeance' => 'Avis d\'échéance',
            'mandat_gestion' => 'Mandat de gestion',
            'inventaire' => 'Inventaire',
            'attestation_loyer' => 'Attestation de loyer',
            'autre' => 'Autre',
        ];
        
        return $types[$this->type] ?? $this->type;
    }

    // Méthodes
    public function getAvailableTags()
    {
        return [
            'Bien' => [
                '{{Bien_Reference}}',
                '{{Bien_Adresse}}',
                '{{Bien_CodePostal}}',
                '{{Bien_Ville}}',
                '{{Bien_Pays}}',
                '{{Bien_Type}}',
                '{{Bien_Surface}}',
                '{{Bien_NombrePieces}}',
                '{{Bien_Etage}}',
                '{{Bien_DPE}}',
            ],
            'Propriétaire' => [
                '{{Proprietaire_Nom}}',
                '{{Proprietaire_Prenom}}',
                '{{Proprietaire_NomComplet}}',
                '{{Proprietaire_Adresse}}',
                '{{Proprietaire_CodePostal}}',
                '{{Proprietaire_Ville}}',
                '{{Proprietaire_Email}}',
                '{{Proprietaire_Telephone}}',
                '{{Proprietaire_IBAN}}',
            ],
            'Locataire' => [
                '{{Locataire_Nom}}',
                '{{Locataire_Prenom}}',
                '{{Locataire_NomComplet}}',
                '{{Locataire_DateNaissance}}',
                '{{Locataire_Adresse}}',
                '{{Locataire_CodePostal}}',
                '{{Locataire_Ville}}',
                '{{Locataire_Email}}',
                '{{Locataire_Telephone}}',
                '{{Locataire_Profession}}',
            ],
            'Contrat' => [
                '{{Contrat_Reference}}',
                '{{Contrat_TypeBail}}',
                '{{Contrat_DateDebut}}',
                '{{Contrat_DateFin}}',
                '{{Contrat_DureeMois}}',
                '{{Contrat_LoyerHC}}',
                '{{Contrat_Charges}}',
                '{{Contrat_LoyerCC}}',
                '{{Contrat_DepotGarantie}}',
                '{{Contrat_DateSignature}}',
            ],
            'Dates' => [
                '{{Date_Aujourd_hui}}',
                '{{Date_Generation}}',
            ],
            'Boucles' => [
                '{{LocataireBlockStart}}',
                '{{LocataireBlockEnd}}',
                '{{GarantBlockStart}}',
                '{{GarantBlockEnd}}',
            ],
        ];
    }

     /**
     * Vérifier si le modèle concerne un type de bien spécifique
     */
    public function concerneBien(string $typeBien): bool
    {
        // Si aucun bien spécifié, le modèle concerne tous les biens
        if (empty($this->biens_concernes)) {
            return true;
        }
        
        return in_array($typeBien, $this->biens_concernes);
    }

    /**
     * Obtenir la liste des types de biens disponibles
     */
    public static function getTypesBiens(): array
    {
        return [
            'appartement' => 'Appartement',
            'maison' => 'Maison',
            'studio' => 'Studio',
            'parking' => 'Parking',
            'garage' => 'Garage',
            'local_commercial' => 'Local commercial',
            'bureau' => 'Bureau',
            'terrain' => 'Terrain',
            'immeuble' => 'Immeuble',
        ];
    }

    /**
     * Obtenir le libellé des biens concernés
     */
    public function getBiensConcernesLibelleAttribute(): string
    {
        if (empty($this->biens_concernes)) {
            return 'Tous les biens';
        }
        
        $typesBiens = self::getTypesBiens();
        $labels = [];
        
        foreach ($this->biens_concernes as $type) {
            $labels[] = $typesBiens[$type] ?? $type;
        }
        
        return implode(', ', $labels);
    }
}