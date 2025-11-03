<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'action',
        'destinataire',
        'details',
        'ip_address',
    ];

    // Relations
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accesseurs
    public function getActionLibelleAttribute()
    {
        $actions = [
            'genere' => 'Généré',
            'modifie' => 'Modifié',
            'envoye' => 'Envoyé',
            'telecharge' => 'Téléchargé',
            'partage' => 'Partagé',
            'supprime' => 'Supprimé',
        ];
        
        return $actions[$this->action] ?? $this->action;
    }
}