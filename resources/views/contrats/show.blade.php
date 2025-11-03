@extends('layouts.app')

@section('title', 'Détails du contrat')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-800">{{ $contrat->reference }}</h1>
                @if($contrat->statut === 'actif')
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">Actif</span>
                @elseif($contrat->statut === 'resilie')
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">Résilié</span>
                @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">{{ ucfirst($contrat->statut) }}</span>
                @endif
                @if($contrat->is_co_location)
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">Co-location</span>
                @endif
            </div>
            <p class="text-gray-600 mt-1">{{ $contrat->type_bail_libelle }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('contrats.index') }}" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded-lg border border-gray-300">
                ← Retour
            </a>
            <a href="{{ route('contrats.edit', $contrat) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Loyer CC</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($contrat->loyer_cc, 2, ',', ' ') }} €</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Loyer annuel</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['loyer_annuel'], 2, ',', ' ') }} €</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Locataires</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['nb_locataires'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Documents</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['nb_documents'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Jours restants</p>
            <p class="text-2xl font-bold {{ $stats['jours_restants'] < 90 ? 'text-red-600' : 'text-gray-900' }}">
                {{ $stats['jours_restants'] > 0 ? $stats['jours_restants'] : 'Expiré' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations du bail -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Informations du bail</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Type de bail</p>
                            <p class="font-medium text-gray-900">{{ $contrat->type_bail_libelle }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Durée</p>
                            <p class="font-medium text-gray-900">{{ $contrat->duree_mois }} mois</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date de début</p>
                            <p class="font-medium text-gray-900">{{ $contrat->date_debut->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date de fin</p>
                            <p class="font-medium text-gray-900">{{ $contrat->date_fin->format('d/m/Y') }}</p>
                        </div>
                        @if($contrat->date_signature)
                            <div>
                                <p class="text-sm text-gray-500">Date de signature</p>
                                <p class="font-medium text-gray-900">{{ $contrat->date_signature->format('d/m/Y') }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Reconduction tacite</p>
                            <p class="font-medium text-gray-900">
                                @if($contrat->tacite_reconduction)
                                    <span class="text-green-600">✓ Oui</span>
                                @else
                                    <span class="text-red-600">✗ Non</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loyer et charges -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Loyer et charges</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Loyer HC</p>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($contrat->loyer_hc, 2, ',', ' ') }} €</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Charges</p>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($contrat->charges, 2, ',', ' ') }} €</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Loyer CC</p>
                            <p class="text-xl font-bold text-blue-600">{{ number_format($contrat->loyer_cc, 2, ',', ' ') }} €</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <p class="text-sm text-gray-500">Dépôt de garantie</p>
                            <p class="font-medium text-gray-900">{{ number_format($contrat->depot_garantie, 2, ',', ' ') }} €</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Périodicité</p>
                            <p class="font-medium text-gray-900">{{ ucfirst($contrat->periodicite_paiement) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jour de paiement</p>
                            <p class="font-medium text-gray-900">Le {{ $contrat->jour_paiement }} du mois</p>
                        </div>
                    </div>
                    @if($contrat->indice_reference || $contrat->date_revision)
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 mt-4">
                            @if($contrat->indice_reference)
                                <div>
                                    <p class="text-sm text-gray-500">Indice de référence</p>
                                    <p class="font-medium text-gray-900">{{ $contrat->indice_reference }}</p>
                                </div>
                            @endif
                            @if($contrat->date_revision)
                                <div>
                                    <p class="text-sm text-gray-500">Date de révision</p>
                                    <p class="font-medium text-gray-900">{{ $contrat->date_revision->format('d/m/Y') }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bien -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Bien loué</h2>
                    <a href="{{ route('biens.show', $contrat->bien) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        Voir la fiche →
                    </a>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        @if($contrat->bien->photos && count($contrat->bien->photos) > 0)
                            <img src="{{ Storage::url($contrat->bien->photos[0]) }}" alt="Photo du bien" 
                                class="w-24 h-24 object-cover rounded-lg">
                        @else
                            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900">{{ $contrat->bien->reference }}</h3>
                            <p class="text-gray-700">{{ $contrat->bien->adresse }}</p>
                            <p class="text-gray-600 text-sm">{{ $contrat->bien->code_postal }} {{ $contrat->bien->ville }}</p>
                            <div class="flex gap-3 mt-2 text-sm text-gray-600">
                                <span>{{ $contrat->bien->type_libelle }}</span>
                                <span>•</span>
                                <span>{{ $contrat->bien->surface }} m²</span>
                                <span>•</span>
                                <span>{{ $contrat->bien->nombre_pieces }} pièces</span>
                                @if($contrat->bien->dpe)
                                    <span>•</span>
                                    <span>DPE: {{ $contrat->bien->dpe }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Locataires -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">
                        Locataire{{ $contrat->locataires->count() > 1 ? 's' : '' }}
                        @if($contrat->is_co_location)
                            <span class="text-sm font-normal text-purple-600">(Co-location)</span>
                        @endif
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($contrat->locataires as $locataire)
                        <div class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                                {{ strtoupper(substr($locataire->prenom, 0, 1)) }}{{ strtoupper(substr($locataire->nom, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-gray-900">{{ $locataire->nom_complet }}</h3>
                                    @if($locataire->pivot->titulaire_principal)
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded">Principal</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600">{{ $locataire->email }}</p>
                                <p class="text-sm text-gray-600">{{ $locataire->telephone }}</p>
                                @if($contrat->is_co_location)
                                    <p class="text-sm text-purple-600 mt-1">
                                        Part du loyer: {{ number_format($locataire->pivot->part_loyer, 2) }}%
                                        ({{ number_format($contrat->loyer_cc * $locataire->pivot->part_loyer / 100, 2, ',', ' ') }} €)
                                    </p>
                                @endif
                                @if($locataire->garants->count() > 0)
                                    <div class="mt-2 text-sm">
                                        <p class="font-medium text-gray-700">Garant(s) :</p>
                                        @foreach($locataire->garants as $garant)
                                            <p class="text-gray-600">• {{ $garant->nom_complet }} ({{ $garant->lien_avec_locataire }})</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('locataires.show', $locataire) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Voir la fiche →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Documents associés -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Documents associés</h2>
                    <button onclick="document.getElementById('generer-document-modal').classList.remove('hidden')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Générer un document
                    </button>
                </div>
                <div class="p-6">
                    @if($contrat->documents->count() > 0)
                        <div class="space-y-3">
                            @foreach($contrat->documents as $doc)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-red-100 rounded flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $doc->nom }}</h4>
                                            <p class="text-sm text-gray-500">{{ $doc->type_libelle }} • {{ $doc->created_at->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('documents.show', $doc) }}" 
                                            class="text-blue-600 hover:text-blue-800 p-2" title="Voir">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('documents.download', $doc) }}" 
                                            class="text-green-600 hover:text-green-800 p-2" title="Télécharger">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p>Aucun document généré pour ce contrat</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Conditions particulières -->
            @if($contrat->conditions_particulieres)
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Conditions particulières</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-line">{{ $contrat->conditions_particulieres }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar actions -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Propriétaire -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Propriétaire</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-bold">
                            {{ strtoupper(substr($contrat->proprietaire->nom_complet, 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $contrat->proprietaire->nom_complet }}</h4>
                            @if($contrat->proprietaire->type === 'societe')
                                <p class="text-xs text-gray-500">Société</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p>{{ $contrat->proprietaire->email }}</p>
                        <p>{{ $contrat->proprietaire->telephone }}</p>
                    </div>
                    <a href="{{ route('proprietaires.show', $contrat->proprietaire) }}" 
                        class="mt-4 block text-center text-blue-600 hover:text-blue-800 text-sm">
                        Voir la fiche →
                    </a>
                </div>
            </div>

            <!-- Actions -->
            @if($contrat->statut === 'actif')
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <button onclick="document.getElementById('renouveler-modal').classList.remove('hidden')"
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                            Renouveler le contrat
                        </button>
                        <button onclick="document.getElementById('resilier-modal').classList.remove('hidden')"
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                            Résilier le contrat
                        </button>
                    </div>
                </div>
            @endif

            <!-- Métadonnées -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="font-bold text-gray-900 mb-3">Métadonnées</h3>
                <div class="text-sm text-gray-600 space-y-2">
                    <p><strong>Créé le :</strong> {{ $contrat->created_at->format('d/m/Y à H:i') }}</p>
                    <p><strong>Modifié le :</strong> {{ $contrat->updated_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            <!-- Danger zone -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <h3 class="font-bold text-red-900 mb-3">Zone dangereuse</h3>
                <p class="text-sm text-red-700 mb-3">La suppression est irréversible</p>
                <form action="{{ route('contrats.destroy', $contrat) }}" method="POST"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce contrat ? Cette action est irréversible.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                        Supprimer le contrat
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ✅ MODAL : Génération automatique à la création -->
@if(session('show_generate_modal'))
<div id="auto-generate-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Contrat créé avec succès !</h3>
            <p class="text-center text-gray-600 mb-6">
                Voulez-vous générer automatiquement le bail correspondant ?
            </p>
            <div class="space-y-3">
                <a href="{{ route('documents.create', ['contrat_id' => $contrat->id]) }}" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-3 rounded-lg font-medium transition-colors">
                    ✅ Oui, générer le bail
                </a>
                <button onclick="document.getElementById('auto-generate-modal').remove()" 
                        class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-700 text-center px-4 py-3 rounded-lg font-medium transition-colors">
                    ⏭️ Plus tard
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal : Générer un document -->
<div id="generer-document-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Générer un document</h3>
                <button onclick="document.getElementById('generer-document-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form action="{{ route('contrats.generer-document', $contrat) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <!-- Sélection du modèle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de document</label>
                    <select name="template_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Sélectionnez un modèle --</option>
                        @php
                            $templates = \App\Models\DocumentTemplate::where('actif', true)
                                ->orderBy('nom')
                                ->get()
                                ->groupBy('type');
                        @endphp
                        @foreach($templates as $type => $templatesByType)
                            <optgroup label="{{ ucfirst(str_replace('_', ' ', $type)) }}">
                                @foreach($templatesByType as $template)
                                    <option value="{{ $template->id }}">
                                        {{ $template->nom }}
                                        @if($template->is_default)
                                            (par défaut)
                                        @endif
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <!-- Format de sortie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format de sortie</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="format" value="pdf" checked
                                class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">PDF</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="format" value="docx"
                                class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">Word (DOCX)</span>
                        </label>
                    </div>
                </div>

                <!-- Informations du contrat -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 mb-2">Informations du contrat</h4>
                    <div class="text-sm text-blue-800 space-y-1">
                        <p><strong>Bien :</strong> {{ $contrat->bien->adresse }}</p>
                        <p><strong>Locataire(s) :</strong> {{ $contrat->locataires->pluck('nom_complet')->join(', ') }}</p>
                        <p><strong>Loyer CC :</strong> {{ number_format($contrat->loyer_cc, 2, ',', ' ') }} €</p>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button"
                    onclick="document.getElementById('generer-document-modal').classList.add('hidden')"
                    class="px-4 py-2 text-gray-700 hover:text-gray-900 border border-gray-300 rounded-lg">
                    Annuler
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Générer le document
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal : Renouveler le contrat -->
<div id="renouveler-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Renouveler le contrat</h3>
                <button onclick="document.getElementById('renouveler-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form action="{{ route('contrats.renouveler', $contrat) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800">
                        Date de fin actuelle : <strong>{{ $contrat->date_fin->format('d/m/Y') }}</strong>
                    </p>
                </div>

                <!-- Nouvelle date de fin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nouvelle date de fin <span class="text-red-600">*</span>
                    </label>
                    <input type="date" name="nouvelle_date_fin" required
                        min="{{ $contrat->date_fin->addDay()->format('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Nouveau loyer (optionnel) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nouveau loyer HC (optionnel)
                    </label>
                    <input type="number" name="nouveau_loyer_hc" step="0.01" min="0"
                        placeholder="{{ number_format($contrat->loyer_hc, 2, ',', ' ') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour conserver le loyer actuel</p>
                </div>

                <!-- Nouvelles charges (optionnel) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nouvelles charges (optionnel)
                    </label>
                    <input type="number" name="nouvelles_charges" step="0.01" min="0"
                        placeholder="{{ number_format($contrat->charges, 2, ',', ' ') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour conserver les charges actuelles</p>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button"
                    onclick="document.getElementById('renouveler-modal').classList.add('hidden')"
                    class="px-4 py-2 text-gray-700 hover:text-gray-900 border border-gray-300 rounded-lg">
                    Annuler
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                    Renouveler
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal : Résilier le contrat -->
<div id="resilier-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Résilier le contrat</h3>
                <button onclick="document.getElementById('resilier-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form action="{{ route('contrats.resilier', $contrat) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-red-800">
                        ⚠️ <strong>Attention :</strong> Cette action mettra fin au contrat et libérera le bien.
                    </p>
                </div>

                <!-- Date de résiliation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Date de résiliation <span class="text-red-600">*</span>
                    </label>
                    <input type="date" name="date_resiliation" required
                        min="{{ $contrat->date_debut->format('Y-m-d') }}"
                        max="{{ $contrat->date_fin->format('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <!-- Motif -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Motif de résiliation
                    </label>
                    <textarea name="motif_resiliation" rows="4"
                        placeholder="Ex: Départ du locataire, vente du bien..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button"
                    onclick="document.getElementById('resilier-modal').classList.add('hidden')"
                    class="px-4 py-2 text-gray-700 hover:text-gray-900 border border-gray-300 rounded-lg">
                    Annuler
                </button>
                <button type="submit"
                    onclick="return confirm('Êtes-vous sûr de vouloir résilier ce contrat ?')"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                    Résilier le contrat
                </button>
            </div>
        </form>
    </div>
</div>

@endsection