<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Afficher la liste des notifications de l'utilisateur
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Notification::with(['contrat.bien', 'bien', 'document'])
            ->pourUtilisateur($user->id)
            ->latest();

        // Filtres
        if ($request->filled('type')) {
            $query->parType($request->type);
        }

        if ($request->filled('priorite')) {
            $query->parPriorite($request->priorite);
        }

        if ($request->filled('statut')) {
            if ($request->statut === 'non_lues') {
                $query->nonLues();
            } elseif ($request->statut === 'lues') {
                $query->lues();
            }
        }

        // Pagination
        $notifications = $query->paginate(20)->withQueryString();

        // Compter les non lues
        $countNonLues = Notification::pourUtilisateur($user->id)->nonLues()->count();

        // Options de filtres
        $types = [
            'relance' => 'Relance de paiement',
            'expiration' => 'Expiration de contrat',
            'revision' => 'Révision de loyer',
            'maintenance' => 'Maintenance',
            'generale' => 'Notification générale',
        ];

        $priorites = [
            'urgente' => 'Urgente',
            'haute' => 'Haute',
            'normale' => 'Normale',
            'basse' => 'Basse',
        ];

        $statuts = [
            'toutes' => 'Toutes',
            'non_lues' => 'Non lues',
            'lues' => 'Lues',
        ];

        return view('notifications.index', compact(
            'notifications',
            'countNonLues',
            'types',
            'priorites',
            'statuts'
        ));
    }

    /**
     * Afficher une notification
     */
    public function show(Notification $notification)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        // Marquer comme lue
        $notification->marquerLue();

        $notification->load(['contrat.bien', 'contrat.locataires', 'bien', 'document']);

        return view('notifications.show', compact('notification'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Notification $notification)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $notification->marquerLue();

        return back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        Notification::pourUtilisateur($user->id)
            ->nonLues()
            ->update([
                'lue' => true,
                'lue_le' => now(),
            ]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Notification $notification)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $notification->delete();

        return back()->with('success', 'Notification supprimée.');
    }

    /**
     * Compter les notifications non lues (pour le badge)
     */
    public function countUnread()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $count = Notification::pourUtilisateur($user->id)->nonLues()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Récentes notifications (pour dropdown)
     */
    public function recent()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notifications = Notification::with(['contrat.bien'])
            ->pourUtilisateur($user->id)
            ->recentes()
            ->latest()
            ->limit(5)
            ->get();

        return response()->json($notifications);
    }
}