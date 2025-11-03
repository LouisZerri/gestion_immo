<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Document;
use App\Models\Locataire;
use App\Models\Proprietaire;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord
     */
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        // Statistiques selon le rôle
        $stats = $this->getStatsForRole($user);

        // Documents récents (selon le rôle)
        $recentDocuments = $this->getRecentDocuments($user);

        // Contrats à renouveler (pour gestionnaires)
        $contratsARenouveler = [];
        if ($user->canManage()) {
            $contratsARenouveler = Contrat::where('statut', 'actif')
                ->whereNotNull('date_fin')
                ->whereDate('date_fin', '<=', now()->addMonths(2))
                ->with(['bien', 'locataires'])
                ->latest('date_fin')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact('stats', 'recentDocuments', 'contratsARenouveler'));
    }

    /**
     * Obtenir les statistiques selon le rôle
     */
    private function getStatsForRole($user)
    {
        if ($user->isSuperAdmin() || $user->isGestionnaire()) {
            // Toutes les statistiques pour admin/gestionnaire
            return [
                'biens' => Bien::count(),
                'biens_loues' => Bien::where('statut', 'loue')->count(),
                'biens_disponibles' => Bien::where('statut', 'disponible')->count(),
                'contrats' => Contrat::where('statut', 'actif')->count(),
                'locataires' => Locataire::count(),
                'proprietaires' => Proprietaire::count(),
                'documents' => Document::count(),
                'documents_mois' => Document::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ];
        } elseif ($user->isProprietaire()) {
            // Statistiques pour un propriétaire
            // TODO: Lier User → Proprietaire avec une relation
            // Pour l'instant, on retourne des stats vides
            return [
                'biens' => 0,
                'contrats' => 0,
                'documents' => 0,
            ];
        } else { // Locataire
            // Statistiques pour un locataire
            // TODO: Lier User → Locataire avec une relation
            // Pour l'instant, on retourne des stats vides
            return [
                'contrats' => 0,
                'documents' => 0,
            ];
        }
    }

    /**
     * Obtenir les documents récents selon le rôle
     */
    private function getRecentDocuments($user)
    {
        if ($user->canManage()) {
            // Tous les documents pour admin/gestionnaire
            return Document::with(['contrat.bien'])
                ->latest()
                ->take(5)
                ->get();
        } elseif ($user->isProprietaire()) {
            // Documents des biens du propriétaire
            // TODO: Filtrer par propriétaire quand la relation User->Proprietaire sera créée
            return collect([]);
        } else { // Locataire
            // Documents du locataire
            // TODO: Filtrer par locataire quand la relation User->Locataire sera créée
            return collect([]);
        }
    }
}