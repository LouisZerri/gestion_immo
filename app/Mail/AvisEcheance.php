<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\Contrat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class AvisEcheance extends Mailable
{
    use Queueable, SerializesModels;

    public Document $document;
    public Contrat $contrat;

    /**
     * Create a new message instance.
     */
    public function __construct(Document $document, Contrat $contrat)
    {
        $this->document = $document;
        $this->contrat = $contrat;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Avis d\'échéance - ' . $this->contrat->bien->adresse,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Calculer la date d'échéance (prochain paiement)
        $dateEcheance = now()->addMonth()->day($this->contrat->jour_paiement);

        return new Content(
            view: 'emails.avis-echeance',
            with: [
                'document' => $this->document,
                'contrat' => $this->contrat,
                'locataire' => $this->contrat->locataire_principal,
                'bien' => $this->contrat->bien,
                'montantTotal' => $this->contrat->loyer_cc,
                'dateEcheance' => $dateEcheance,
                'periode' => $dateEcheance->format('F Y'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Ajouter le PDF en pièce jointe
        if (Storage::exists($this->document->file_path)) {
            $attachments[] = Attachment::fromStorage($this->document->file_path)
                ->as('Avis_Echeance_' . $this->contrat->reference . '_' . now()->format('Y-m') . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}