<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;

class DocumentShareController extends Controller
{
    /**
     * Afficher le formulaire de partage
     */
    public function create(Document $document)
    {
        // Vérifier les permissions
        if (!auth()->user()->canManage()) {
            abort(403, 'Vous n\'avez pas les permissions pour partager ce document.');
        }

        // Récupérer tous les utilisateurs actifs (sauf l'utilisateur connecté)
        $users = User::where('id', '!=', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // IDs des utilisateurs avec qui c'est déjà partagé
        $sharedUserIds = $document->shared_with ?? [];

        return view('documents.share', compact('document', 'users', 'sharedUserIds'));
    }

    /**
     * Enregistrer le partage
     */
    public function store(Request $request, Document $document)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'permissions' => 'required|in:view,download',
        ], [
            'user_ids.required' => 'Sélectionnez au moins un utilisateur.',
            'user_ids.*.exists' => 'Un ou plusieurs utilisateurs n\'existent pas.',
            'permissions.required' => 'Sélectionnez les permissions de partage.',
        ]);

        // Partager le document
        $document->shareWith($validated['user_ids'], $validated['permissions']);

        // Log de l'action
        $document->logAction('shared', auth()->id(), 'Partagé avec ' . count($validated['user_ids']) . ' utilisateur(s)');

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document partagé avec succès avec ' . count($validated['user_ids']) . ' utilisateur(s) !');
    }

    /**
     * Retirer le partage
     */
    public function destroy(Document $document)
    {
        $document->unshare();

        // Log
        $document->logAction('unshared', auth()->id(), 'Partage retiré');

        return back()->with('success', 'Le partage a été retiré avec succès.');
    }
}