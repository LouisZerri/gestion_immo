<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EdlElement extends Model
{
    use HasFactory;

    protected $table = 'edl_elements';

    protected $fillable = [
        'edl_piece_id',
        'element',
        'nature',
        'etat_usure',
        'fonctionnement',
        'commentaires',
        'ordre',
    ];

    /**
     * Relations
     */
    public function piece(): BelongsTo
    {
        return $this->belongsTo(EdlPiece::class, 'edl_piece_id');
    }

    /**
     * Méthodes utiles
     */
    public function isComplete(): bool
    {
        return !empty($this->nature) || !empty($this->etat_usure) || !empty($this->fonctionnement) || !empty($this->commentaires);
    }

    public function isEmpty(): bool
    {
        return !$this->isComplete();
    }
}