<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EtatDesLieux extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etats_des_lieux';

    /**
     * Nom de la clé pour le route model binding
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    protected $fillable = [
        'bien_id',
        'contrat_id',
        'type',
        'statut',
        'date_etat',
        'observations_generales',
        'compteurs_eau',
        'compteurs_gaz',
        'compteurs_electricite',
        'chauffage',
        'eau_chaude',
        'cles',
        'autres_amenagements',
        'document_id',
    ];

    protected $casts = [
        'date_etat' => 'date',
        'compteurs_eau' => 'array',
        'compteurs_gaz' => 'array',
        'compteurs_electricite' => 'array',
        'chauffage' => 'array',
        'eau_chaude' => 'array',
        'cles' => 'array',
        'autres_amenagements' => 'array',
    ];

    /**
     * Relations
     */
    public function bien(): BelongsTo
    {
        return $this->belongsTo(Bien::class);
    }

    public function contrat(): BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(EdlPiece::class, 'etat_des_lieux_id')->orderBy('ordre');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Accesseurs
     */
    public function getTypeLibelleAttribute(): string
    {
        return match($this->type) {
            'entree' => 'État des lieux d\'entrée',
            'sortie' => 'État des lieux de sortie',
            null => 'Non défini',
            default => $this->type ?? 'Non défini',
        };
    }

    public function getStatutLibelleAttribute(): string
    {
        return match($this->statut) {
            'brouillon' => 'Brouillon',
            'termine' => 'Terminé',
            null => 'Non défini',
            default => $this->statut ?? 'Non défini',
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'brouillon' => 'bg-yellow-100 text-yellow-800',
            'termine' => 'bg-green-100 text-green-800',
            null => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Scopes
     */
    public function scopeEntree($query)
    {
        return $query->where('type', 'entree');
    }

    public function scopeSortie($query)
    {
        return $query->where('type', 'sortie');
    }

    public function scopeTermine($query)
    {
        return $query->where('statut', 'termine');
    }

    public function scopeBrouillon($query)
    {
        return $query->where('statut', 'brouillon');
    }

    /**
     * Méthodes utiles
     */
    public function isTermine(): bool
    {
        return $this->statut === 'termine';
    }

    public function isBrouillon(): bool
    {
        return $this->statut === 'brouillon';
    }

    public function marquerTermine(): bool
    {
        return $this->update(['statut' => 'termine']);
    }

    public function getTotalPhotos(): int
    {
        return $this->pieces->sum(function($piece) {
            return count($piece->photos ?? []);
        });
    }
}