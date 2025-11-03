<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'type',
        'format',
        'file_path',
        'file_type',
        'file_size',
        'bien_id',
        'contrat_id',
        'locataire_id',
        'proprietaire_id',
        'template_id',
        'statut',
        'is_shared',
        'shared_with',
        'share_permissions',
        'date_envoi',
        'notes',
        'is_uploaded',
        'original_filename',
        'photos',  
    ];

    protected $casts = [
        'is_shared' => 'boolean',
        'shared_with' => 'array',
        'is_uploaded' => 'boolean',
        'photos' => 'array',
        'date_envoi' => 'date',
    ];

    // ✅ Attributs par défaut
    protected $attributes = [
        'format' => 'pdf',
        'statut' => 'genere',
        'is_shared' => false,
    ];

    // Relations
    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    public function logs()
    {
        return $this->hasMany(DocumentLog::class);
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
            'document_externe' => 'Document externe',
            'autre' => 'Autre',
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getFormatLibelleAttribute()
    {
        $formats = [
            'pdf' => 'PDF',
            'docx' => 'Word (DOCX)',
        ];

        return $formats[$this->format] ?? strtoupper($this->format);
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) return 'N/A';

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($this->file_size, 1024));
        return round($this->file_size / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    public function getDownloadUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    // Méthodes
    public function logAction($action, $userId = null, $details = null, $destinataire = null)
    {
        /** @var \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard $auth */
        $auth = auth();

        return $this->logs()->create([
            'user_id' => $userId ?? $auth->id(),
            'action' => $action,
            'destinataire' => $destinataire,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }

    /** 
     * 9Vérifier si un utilisateur a accès au document
     */
    public function canAccess($userId): bool
    {
        if (!$this->is_shared || empty($this->shared_with)) {
            return false;
        }
        
        return in_array($userId, $this->shared_with);
    }

    /**
     * Partager le document avec des utilisateurs
     */
    public function shareWith(array $userIds, string $permissions = 'view'): void
    {
        $currentShared = $this->shared_with ?? [];
        
        $this->update([
            'shared_with' => array_unique(array_merge($currentShared, $userIds)),
            'is_shared' => true,
            'share_permissions' => $permissions,
        ]);
    }

    /**
     * Retirer le partage
     */
    public function unshare(): void
    {
        $this->update([
            'shared_with' => null,
            'is_shared' => false,
            'share_permissions' => 'view',
        ]);
    }

    /**
     * Ajouter des photos (pour état des lieux)
     */
    public function addPhotos(array $photoPaths): void
    {
        $currentPhotos = $this->photos ?? [];
        $this->update([
            'photos' => array_merge($currentPhotos, $photoPaths),
        ]);
    }

    /**
     * Obtenir les utilisateurs partagés avec leurs noms
     */
    public function getSharedUsersWithNames()
    {
        if (empty($this->shared_with)) {
            return collect([]);
        }
        
        return \App\Models\User::whereIn('id', $this->shared_with)->get();
    }
}