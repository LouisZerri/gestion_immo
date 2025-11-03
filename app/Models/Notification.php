<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'user_id',
        'contrat_id',
        'bien_id',
        'document_id',
        'titre',
        'message',
        'lue',
        'envoyee_par_email',
        'lue_le',
        'envoyee_le',
        'priorite',
        'metadata',
    ];

    protected $casts = [
        'lue' => 'boolean',
        'envoyee_par_email' => 'boolean',
        'lue_le' => 'datetime',
        'envoyee_le' => 'datetime',
        'metadata' => 'array',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // Accesseurs
    public function getTypeLibelleAttribute()
    {
        return match($this->type) {
            'relance' => 'Relance de paiement',
            'expiration' => 'Expiration de contrat',
            'revision' => 'Révision de loyer',
            'maintenance' => 'Maintenance',
            'generale' => 'Notification générale',
            default => $this->type,
        };
    }

    public function getPrioriteColorAttribute()
    {
        return match($this->priorite) {
            'urgente' => 'red',
            'haute' => 'orange',
            'normale' => 'blue',
            'basse' => 'gray',
            default => 'gray',
        };
    }

    public function getPrioriteIconAttribute()
    {
        return match($this->priorite) {
            'urgente' => '🔴',
            'haute' => '🟠',
            'normale' => '🔵',
            'basse' => '⚪',
            default => '⚪',
        };
    }

    // Méthodes métier
    
    /**
     * Marquer comme lue
     */
    public function marquerLue(): void
    {
        if (!$this->lue) {
            $this->update([
                'lue' => true,
                'lue_le' => now(),
            ]);
        }
    }

    /**
     * Marquer comme envoyée par email
     */
    public function marquerEnvoyee(): void
    {
        $this->update([
            'envoyee_par_email' => true,
            'envoyee_le' => now(),
        ]);
    }

    // Scopes
    
    /**
     * Scope : Notifications non lues
     */
    public function scopeNonLues($query)
    {
        return $query->where('lue', false);
    }

    /**
     * Scope : Notifications lues
     */
    public function scopeLues($query)
    {
        return $query->where('lue', true);
    }

    /**
     * Scope : Par type
     */
    public function scopeParType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope : Par priorité
     */
    public function scopeParPriorite($query, string $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    /**
     * Scope : Récentes (7 derniers jours)
     */
    public function scopeRecentes($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7));
    }

    /**
     * Scope : Pour un utilisateur
     */
    public function scopePourUtilisateur($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helpers statiques
    
    /**
     * Créer une notification de relance
     */
    public static function creerRelance(int $userId, int $contratId, int $documentId, array $data = []): self
    {
        return self::create([
            'type' => 'relance',
            'user_id' => $userId,
            'contrat_id' => $contratId,
            'document_id' => $documentId,
            'titre' => $data['titre'] ?? 'Relance de paiement',
            'message' => $data['message'] ?? 'Le loyer n\'a pas été réglé dans les délais.',
            'priorite' => $data['priorite'] ?? 'haute',
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    /**
     * Créer une notification d'expiration
     */
    public static function creerExpiration(int $userId, int $contratId, array $data = []): self
    {
        return self::create([
            'type' => 'expiration',
            'user_id' => $userId,
            'contrat_id' => $contratId,
            'titre' => $data['titre'] ?? 'Contrat bientôt expiré',
            'message' => $data['message'] ?? 'Le contrat arrive à échéance dans 3 mois.',
            'priorite' => $data['priorite'] ?? 'normale',
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    /**
     * Créer une notification de révision
     */
    public static function creerRevision(int $userId, int $contratId, array $data = []): self
    {
        return self::create([
            'type' => 'revision',
            'user_id' => $userId,
            'contrat_id' => $contratId,
            'titre' => $data['titre'] ?? 'Révision de loyer due',
            'message' => $data['message'] ?? 'La date de révision annuelle du loyer est atteinte.',
            'priorite' => $data['priorite'] ?? 'normale',
            'metadata' => $data['metadata'] ?? [],
        ]);
    }
}