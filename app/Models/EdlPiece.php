<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdlPiece extends Model
{
    use HasFactory;

    protected $table = 'edl_pieces';

    protected $fillable = [
        'etat_des_lieux_id',
        'nom_piece',
        'ordre',
        'commentaires_piece',
        'photos',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    /**
     * Relations
     */
    public function etatDesLieux(): BelongsTo
    {
        return $this->belongsTo(EtatDesLieux::class, 'etat_des_lieux_id');
    }

    public function elements(): HasMany
    {
        return $this->hasMany(EdlElement::class)->orderBy('ordre');
    }

    /**
     * Méthodes utiles
     */
    public function addPhoto(string $photoPath): void
    {
        $photos = $this->photos ?? [];
        $photos[] = $photoPath;
        $this->update(['photos' => $photos]);
    }

    public function removePhoto(string $photoPath): void
    {
        $photos = $this->photos ?? [];
        $photos = array_values(array_diff($photos, [$photoPath]));
        $this->update(['photos' => $photos]);
    }

    public function hasPhotos(): bool
    {
        return !empty($this->photos);
    }

    public function getPhotosCount(): int
    {
        return count($this->photos ?? []);
    }
}