<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ========================================
    // ACCESSEURS
    // ========================================

    /**
     * Obtenir les initiales pour l'avatar
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Obtenir le libellé du rôle
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'super_admin' => 'Super Administrateur',
            'gestionnaire' => 'Gestionnaire',
            'proprietaire' => 'Propriétaire',
            'locataire' => 'Locataire',
            default => 'Utilisateur',
        };
    }

    // ========================================
    // MÉTHODES DE PERMISSIONS
    // ========================================

    /**
     * Vérifier si l'utilisateur est super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Vérifier si l'utilisateur est gestionnaire
     */
    public function isGestionnaire(): bool
    {
        return in_array($this->role, ['super_admin', 'gestionnaire']);
    }

    /**
     * Vérifier si l'utilisateur est propriétaire
     */
    public function isProprietaire(): bool
    {
        return $this->role === 'proprietaire';
    }

    /**
     * Vérifier si l'utilisateur est locataire
     */
    public function isLocataire(): bool
    {
        return $this->role === 'locataire';
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    /**
     * Vérifier si l'utilisateur peut accéder à la gestion
     */
    public function canManage(): bool
    {
        return in_array($this->role, ['super_admin', 'gestionnaire']);
    }
}