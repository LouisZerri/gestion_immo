<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QuittanceAutomatisee extends Model
{
    use HasFactory;

    protected $table = 'quittances_automatisees';

    protected $fillable = [
        'contrat_id',
        'template_id',
        'actif',
        'type',
        'periodicite',
        'jour_generation',
        'envoi_automatique',
        'email_destinataire',
        'date_debut',
        'date_fin',
        'derniere_generation',
        'prochaine_generation',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'envoi_automatique' => 'boolean',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'derniere_generation' => 'date',
        'prochaine_generation' => 'date',
    ];

    // Relations
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /**
     * Quittances générées (Documents)
     */
    public function quittancesGenerees()
    {
        return $this->contrat->documents()
            ->where('type', $this->type)
            ->orderBy('created_at', 'desc');
    }

    // Accesseurs
    public function getTypeLibelleAttribute()
    {
        return match($this->type) {
            'quittance' => 'Quittance de loyer',
            'avis_echeance' => 'Avis d\'échéance',
            default => $this->type,
        };
    }

    public function getPeriodiciteLibelleAttribute()
    {
        return match($this->periodicite) {
            'mensuelle' => 'Mensuelle',
            'trimestrielle' => 'Trimestrielle',
            'annuelle' => 'Annuelle',
            default => $this->periodicite,
        };
    }

    // Méthodes métier
    
    /**
     * Vérifier si une génération est due aujourd'hui
     */
    public function estDueAujourdhui(): bool
    {
        if (!$this->actif) {
            return false;
        }

        // Si prochaine_generation est définie et est aujourd'hui ou passée
        if ($this->prochaine_generation && $this->prochaine_generation <= now()) {
            return true;
        }

        // Sinon, vérifier si c'est le jour de génération du mois
        if (now()->day == $this->jour_generation) {
            // Vérifier qu'on n'a pas déjà généré ce mois-ci
            if ($this->derniere_generation && $this->derniere_generation->isSameMonth(now())) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Calculer la prochaine date de génération
     */
    public function calculerProchaineGeneration(): ?Carbon
    {
        $now = now();
        
        // Si on a déjà généré ce mois et c'est avant le jour de génération
        if ($this->derniere_generation && $this->derniere_generation->isSameMonth($now)) {
            $prochaine = $now->copy()->addMonth()->day($this->jour_generation);
        } else {
            // Sinon, prochaine génération = ce mois au jour défini
            $prochaine = $now->copy()->day($this->jour_generation);
            
            // Si le jour est déjà passé ce mois-ci, passer au mois suivant
            if ($prochaine < $now) {
                $prochaine->addMonth();
            }
        }

        // Ajuster selon la périodicité
        if ($this->periodicite === 'trimestrielle') {
            $prochaine = $this->derniere_generation 
                ? $this->derniere_generation->copy()->addMonths(3)
                : $prochaine;
        } elseif ($this->periodicite === 'annuelle') {
            $prochaine = $this->derniere_generation 
                ? $this->derniere_generation->copy()->addYear()
                : $prochaine;
        }

        return $prochaine;
    }

    /**
     * Marquer comme générée aujourd'hui
     */
    public function marquerGeneree(): void
    {
        $this->update([
            'derniere_generation' => now(),
            'prochaine_generation' => $this->calculerProchaineGeneration(),
        ]);
    }

    /**
     * Scope : Quittances actives
     */
    public function scopeActives($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope : Quittances dues aujourd'hui
     */
    public function scopeDuesAujourdhui($query)
    {
        return $query->actives()
            ->where(function($q) {
                $q->whereDate('prochaine_generation', '<=', now())
                  ->orWhere(function($q2) {
                      $q2->whereDay('jour_generation', now()->day)
                         ->where(function($q3) {
                             $q3->whereNull('derniere_generation')
                                ->orWhere(function($q4) {
                                    $q4->whereMonth('derniere_generation', '!=', now()->month)
                                       ->orWhereYear('derniere_generation', '!=', now()->year);
                                });
                         });
                  });
            });
    }
}